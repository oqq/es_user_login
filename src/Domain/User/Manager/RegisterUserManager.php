<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Manager;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\Command\CreateIdentityForNewUser;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\User\Command\CreateUser;
use Oqq\EsUserLogin\Domain\User\Command\RegisterUser;
use Oqq\EsUserLogin\Domain\User\UserId;
use Prooph\ServiceBus\CommandBus;

final class RegisterUserManager
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(RegisterUser $command): void
    {
        $this->createUser($command->userId(), $command->emailAddress());
        $this->createIdentity($command->emailAddress(), $command->userId(), $command->password());
    }

    private function createIdentity(EmailAddress $emailAddress, UserId $userId, Password $password): void
    {
        $identityId = IdentityId::fromEmailAddress($emailAddress);

        $this->commandBus->dispatch(
            CreateIdentityForNewUser::withPassword($identityId, $userId, $password)
        );
    }

    private function createUser(UserId $userId, EmailAddress $emailAddress): void
    {
        $this->commandBus->dispatch(new CreateUser([
            'user_id' => $userId->toString(),
            'email_address' => $emailAddress->toString(),
        ]));
    }
}
