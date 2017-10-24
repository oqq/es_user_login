<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\Command\RegisterUser;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Domain\User\UserRepository;

final class RegisterUserHandler
{
    private $identityRepository;
    private $userRepository;
    private $hashService;

    public function __construct(
        IdentityRepository $identityRepository,
        UserRepository $userRepository,
        PasswordHashService $hashService
    ) {
        $this->identityRepository = $identityRepository;
        $this->userRepository = $userRepository;
        $this->hashService = $hashService;
    }

    public function __invoke(RegisterUser $command): void
    {
        $identityId = IdentityId::fromEmailAddress($command->emailAddress());
        $identity = $this->identityRepository->load($identityId);

        // email address is already in use
        if ($identity) {
            $this->saveRegisterAttempt($identity->userId(), $command->emailAddress());

            return;
        }

        $this->createNewUserOrFail($identityId, $command->userId(), $command->emailAddress(), $command->password());
    }

    private function saveRegisterAttempt(UserId $userId, EmailAddress $emailAddress): void
    {
        $user = $this->userRepository->get($userId);
        $user->registerAgain($emailAddress);

        $this->userRepository->save($user);
    }

    private function createNewUserOrFail(
        IdentityId $identityId,
        UserId $userId,
        EmailAddress $emailAddress,
        Password $password
    ): void {
        $user = User::register($userId, $emailAddress);

        $this->createNewIdentityForUser($identityId, $user, $password);
        $this->userRepository->save($user);
    }

    private function createNewIdentityForUser(IdentityId $identityId, User $user, $password): void
    {
        $identity = Identity::createForUserWithPassword($identityId, $user, $password, $this->hashService);
        $this->identityRepository->save($identity);
    }
}
