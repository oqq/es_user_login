<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\User\Command\Logout;
use Oqq\EsUserLogin\Domain\User\UserRepository;

final class LogoutHandler
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Logout $command): void
    {
        $user = $this->userRepository->get($command->userId());
        $user->logout();

        $this->userRepository->save($user);
    }
}
