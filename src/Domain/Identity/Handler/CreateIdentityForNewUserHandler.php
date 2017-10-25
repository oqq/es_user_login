<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Handler;

use Oqq\EsUserLogin\Domain\Identity\Command\CreateIdentityForNewUser;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\UserId;

final class CreateIdentityForNewUserHandler
{
    private $identityRepository;
    private $hashService;

    public function __construct(IdentityRepository $identityRepository, PasswordHashService $hashService)
    {
        $this->identityRepository = $identityRepository;
        $this->hashService = $hashService;
    }

    public function __invoke(CreateIdentityForNewUser $command): void
    {
        if ($this->identityIsOccupied($command->identityId(), $command->userId())) {
            return;
        }

        $identity = Identity::createForNewUser(
            $command->identityId(),
            $command->userId(),
            $command->password(),
            $this->hashService
        );

        $this->identityRepository->save($identity);
    }

    private function identityIsOccupied(IdentityId $identityId, UserId $userId): bool
    {
        $identity = $this->identityRepository->load($identityId);

        if (!$identity) {
            return false;
        }

        $identity->registerReuseForNewUser($userId);
        $this->identityRepository->save($identity);

        return true;
    }
}
