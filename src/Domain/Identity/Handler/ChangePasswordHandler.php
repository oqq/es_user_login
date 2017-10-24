<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Handler;

use Oqq\EsUserLogin\Domain\Identity\Command\ChangePassword;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\PasswordHashService;

final class ChangePasswordHandler
{
    private $identityRepository;
    private $hashService;

    public function __construct(IdentityRepository $identityRepository, PasswordHashService $hashService)
    {
        $this->identityRepository = $identityRepository;
        $this->hashService = $hashService;
    }

    public function __invoke(ChangePassword $command): void
    {
        $identity = $this->identityRepository->load($command->identityId());

        if (!$identity) {
            return;
        }

        $identity->changePassword($command->currentPassword(), $command->newPassword(), $this->hashService);

        $this->identityRepository->save($identity);
    }
}
