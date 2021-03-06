<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/JWTCreatedSubscriber.php
 */

namespace App\EventSubscriber;

use App\Security\SecurityUser;
use App\Service\LocalizationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;
use DateTimeZone;

/**
 * Class JWTCreatedSubscriber
 *
 * @package App\EventSubscriber
 */
class JWTCreatedSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param RequestStack    $requestStack
     * @param LoggerInterface $logger
     */
    public function __construct(RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array<string, string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            JWTCreatedEvent::class => 'onJWTCreated',
            Events::JWT_CREATED => 'onJWTCreated',
        ];
    }

    /**
     * Subscriber method to attach some custom data to current JWT payload.
     *
     * This method is called when 'lexik_jwt_authentication.on_jwt_created' event is broadcast.
     *
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        // Get current original payload
        $payload = $event->getData();
        // Set localization data
        $this->setLocalizationData($payload, $event->getUser());
        // Update JWT expiration data
        $this->setExpiration($payload);
        // Add some extra security data to payload
        $this->setSecurityData($payload);
        // And set new payload for JWT
        $event->setData($payload);
    }

    /**
     * @param array                      $payload
     * @param UserInterface|SecurityUser $user
     */
    private function setLocalizationData(array &$payload, UserInterface $user): void
    {
        $payload['language'] = $user instanceof SecurityUser
            ? $user->getLanguage()
            : LocalizationService::DEFAULT_LANGUAGE;
        $payload['locale'] = $user instanceof SecurityUser
            ? $user->getLocale()
            : LocalizationService::DEFAULT_LOCALE;
        $payload['timezone'] = $user instanceof SecurityUser
            ? $user->getTimezone()
            : LocalizationService::DEFAULT_TIMEZONE;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Method to set/modify JWT expiration date dynamically.
     *
     * @param array<string, string|int> $payload
     */
    private function setExpiration(array &$payload): void
    {
        // Set new exp value for JWT
        /** @noinspection PhpUnhandledExceptionInspection */
        $payload['exp'] = (new DateTime('+1 day', new DateTimeZone('UTC')))->getTimestamp();
    }

    /**
     * Method to add some security related data to JWT payload, which are checked on JWT decode process.
     *
     * @see JWTDecodedListener
     *
     * @param array<string, string|int> $payload
     */
    private function setSecurityData(array &$payload): void
    {
        // Get current request
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            $this->logger->alert('Request not available');

            return;
        }

        // Get bits for checksum calculation
        $bits = [
            $request->getClientIp(),
            $request->headers->get('User-Agent'),
        ];
        // Attach checksum to JWT payload
        $payload['checksum'] = hash('sha512', implode('|', $bits));
    }
}
