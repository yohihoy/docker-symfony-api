<?php
declare(strict_types = 1);
/**
 * /src/Security/Provider/SecurityUserFactory.php
 */

namespace App\Security\Provider;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\RolesService;
use App\Security\SecurityUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Throwable;

/**
 * Class SecurityUserFactory
 *
 * @package App\Security\Provider
 */
class SecurityUserFactory implements UserProviderInterface
{
    private UserRepository $userRepository;
    private RolesService $rolesService;
    private string $uuidV1Regex;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     * @param RolesService   $rolesService
     * @param string         $uuidV1Regex
     */
    public function __construct(UserRepository $userRepository, RolesService $rolesService, string $uuidV1Regex)
    {
        $this->userRepository = $userRepository;
        $this->rolesService = $rolesService;
        $this->uuidV1Regex = $uuidV1Regex;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @throws Throwable
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->userRepository->loadUserByUsername(
            $username,
            (bool)preg_match('#' . $this->uuidV1Regex . '#', $username)
        );

        if (!($user instanceof User)) {
            throw new UsernameNotFoundException(sprintf('User not found for UUID: "%s".', $username));
        }

        return (new SecurityUser($user))->setRoles($this->rolesService->getInheritedRoles($user->getRoles()));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return $class === SecurityUser::class;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     *
     * @return SecurityUser
     */
    public function refreshUser(UserInterface $user): SecurityUser
    {
        if (!($user instanceof SecurityUser)) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $userEntity = $this->userRepository->find($user->getUsername());

        if (!($userEntity instanceof User)) {
            throw new UsernameNotFoundException(sprintf('User not found for UUID: "%s".', $user->getUsername()));
        }

        return (new SecurityUser($userEntity))
            ->setRoles($this->rolesService->getInheritedRoles($userEntity->getRoles()));
    }
}
