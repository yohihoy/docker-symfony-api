<?php
declare(strict_types = 1);
/**
 * /src/Command/User/CreateUserCommand.php
 */

namespace App\Command\User;

use Symfony\Component\Console\Command\Command;
use App\Command\Traits\ApiKeyUserManagementHelper;
use App\Command\Traits\StyleSymfony;
use App\Command\HelperConfigure;
use App\DTO\User\UserCreate as UserDto;
use App\Form\Type\Console\UserType;
use App\Repository\RoleRepository;
use App\Resource\UserGroupResource;
use App\Resource\UserResource;
use App\Security\RolesService;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * Class CreateUserCommand
 *
 * @package App\Command\User
 */
class CreateUserCommand extends Command
{
    // Traits
    use ApiKeyUserManagementHelper;
    use StyleSymfony;

    private const PARAMETER_NAME = 'name';
    private const PARAMETER_DESCRIPTION = 'description';

    /**
     * @var array<int, array<string, int|string>>
     */
    private static array $commandParameters = [
        [
            self::PARAMETER_NAME => 'username',
            self::PARAMETER_DESCRIPTION => 'Username',
        ],
        [
            self::PARAMETER_NAME => 'firstName',
            self::PARAMETER_DESCRIPTION => 'First name of the user',
        ],
        [
            self::PARAMETER_NAME => 'lastName',
            self::PARAMETER_DESCRIPTION => 'Last name of the user',
        ],
        [
            self::PARAMETER_NAME => 'email',
            self::PARAMETER_DESCRIPTION => 'Email of the user',
        ],
        [
            self::PARAMETER_NAME => 'plainPassword',
            self::PARAMETER_DESCRIPTION => 'Plain password for user',
        ],
        [
            self::PARAMETER_NAME => 'userGroups',
            self::PARAMETER_DESCRIPTION => 'User groups where to attach user',
        ],
    ];

    private UserResource $userResource;
    private UserGroupResource $userGroupResource;
    private RolesService $rolesService;
    private RoleRepository $roleRepository;


    /**
     * Constructor
     *
     * @param UserResource      $userResource
     * @param UserGroupResource $userGroupResource
     * @param RolesService      $rolesService
     * @param RoleRepository    $roleRepository
     *
     * @throws LogicException
     */
    public function __construct(
        UserResource $userResource,
        UserGroupResource $userGroupResource,
        RolesService $rolesService,
        RoleRepository $roleRepository
    ) {
        parent::__construct('user:create');

        $this->userResource = $userResource;
        $this->userGroupResource = $userGroupResource;
        $this->rolesService = $rolesService;
        $this->roleRepository = $roleRepository;

        $this->setDescription('Console command to create user to database');
    }

    /**
     * Getter for RolesService
     *
     * @return RolesService
     */
    public function getRolesService(): RolesService
    {
        return $this->rolesService;
    }

    /**
     * Configures the current command.
     *
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        parent::configure();
        HelperConfigure::configure($this, self::$commandParameters);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        // Check that roles exists
        $this->checkUserGroups($output, $input->isInteractive(), $io);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var UserDto $dto */
        $dto = $helper->interactUsingForm(UserType::class, $input, $output);
        // Create new user
        $this->userResource->create($dto);

        if ($input->isInteractive()) {
            $io->success('User created - have a nice day');
        }

        return 0;
    }

    /**
     * Method to check if database contains user groups, if non exists method will run 'user:create-group' command
     * to create those automatically according to '$this->roles->getRoles()' output. Basically this will automatically
     * create user groups for each role that is defined to application.
     *
     * Also note that if groups are not found method will reset application 'role' table content, so that we can be
     * sure that we can create all groups correctly.
     *
     * @param OutputInterface $output
     * @param bool $interactive
     * @param SymfonyStyle $io
     *
     * @throws Throwable
     */
    private function checkUserGroups(OutputInterface $output, bool $interactive, SymfonyStyle $io): void
    {
        if ($this->userGroupResource->count() !== 0) {
            return;
        }

        if ($interactive) {
            $io->block('User groups are not yet created, creating those now...');
        }

        // Reset roles
        $this->roleRepository->reset();
        // Create user groups for each role
        $this->createUserGroups($output);
    }
}
