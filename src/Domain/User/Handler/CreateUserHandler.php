<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\User\Command\CreateUser;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserRepository;

final class CreateUserHandler
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(CreateUser $command): void
    {
        $user = User::register($command->userId(), $command->emailAddress());

        $this->userRepository->save($user);
    }
}
