<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\Command\Login;
use Oqq\EsUserLogin\Domain\User\UserRepository;

final class LoginHandler
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

    public function __invoke(Login $command): void
    {
        $identityId = IdentityId::fromEmailAddress($command->emailAddress());
        $identity = $this->identityRepository->load($identityId);

        if (!$identity) {
            return;
        }

        $user = $this->userRepository->get($identity->userId());
        $user->loginWithIdentity($identity, $command->password(), $this->hashService);

        $this->userRepository->save($user);
        $this->identityRepository->save($identity);
    }
}
