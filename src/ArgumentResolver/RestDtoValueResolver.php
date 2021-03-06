<?php
declare(strict_types = 1);
/**
 * /src/ArgumentResolver/RestDtoValueResolver.php
 */

namespace App\ArgumentResolver;

use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use App\Rest\Controller;
use App\Rest\ControllerCollection;
use AutoMapperPlus\AutoMapperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use App\DTO\Interfaces\RestDtoInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use Generator;

/**
 * Class RestDtoValueResolver
 *
 * @package App\ArgumentResolver
 */
class RestDtoValueResolver implements ArgumentValueResolverInterface
{
    /**
     * string
     */
    private const CONTROLLER_KEY = '_controller';

    /**
     * @var array<int, string>
     */
    private array $supportedActions = [
        Controller::ACTION_CREATE,
        Controller::ACTION_UPDATE,
        Controller::ACTION_PATCH,
    ];

    /**
     * @var array<string, string>
     */
    private array $actionMethodMap = [
        Controller::ACTION_CREATE => Controller::METHOD_CREATE,
        Controller::ACTION_UPDATE => Controller::METHOD_UPDATE,
        Controller::ACTION_PATCH => Controller::METHOD_PATCH,
    ];

    private ControllerCollection $controllerCollection;
    private AutoMapperInterface $autoMapper;


    /**
     * Constructor
     *
     * @param ControllerCollection $controllerCollection
     * @param AutoMapperInterface  $autoMapper
     */
    public function __construct(ControllerCollection $controllerCollection, AutoMapperInterface $autoMapper)
    {
        $this->controllerCollection = $controllerCollection;
        $this->autoMapper = $autoMapper;
    }

    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     *
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (count(explode('::', (string)$request->attributes->get(self::CONTROLLER_KEY))) !== 2) {
            return false;
        }

        [$controllerName, $actionName] = explode('::', (string)$request->attributes->get(self::CONTROLLER_KEY));

        return $argument->getType() === RestDtoInterface::class
            && in_array($actionName, $this->supportedActions, true)
            && $this->controllerCollection->has($controllerName);
    }

    /**
     * Returns the possible value(s).
     *
     * @param Request          $request
     * @param ArgumentMetadata $argumentMetadata
     *
     * @throws UnregisteredMappingException
     *
     * @return Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argumentMetadata): Generator
    {
        [$controllerName, $actionName] = explode('::', (string)$request->attributes->get(self::CONTROLLER_KEY));

        $dtoClass = $this->controllerCollection
            ->get($controllerName)
            ->getDtoClass((string)$this->actionMethodMap[$actionName]);

        yield $this->autoMapper->map($request, $dtoClass);
    }
}
