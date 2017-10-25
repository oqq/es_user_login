<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity;

use Oqq\EsUserLogin\Domain\AggregateRoot;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\UserId;
use Prooph\EventSourcing\AggregateChanged;

final class Identity extends AggregateRoot
{
    /** @var IdentityId */
    private $identityId;

    /** @var UserId */
    private $userId;

    /** @var PasswordHash */
    private $passwordHash;

    public static function createForNewUser(
        IdentityId $identityId,
        UserId $userId,
        Password $password,
        PasswordHashService $hashService
    ): self {
        $identity = new self();

        $identity->recordThat(Event\IdentityWasCreatedForNewUser::withUserIdAndPassword(
            $identityId,
            $userId,
            $password->hash($hashService)
        ));

        return $identity;
    }

    public function registerReuseForNewUser(UserId $userId): void
    {
        $this->recordThat(Event\IdentityWasReusedForNewUser::withUserId($this->identityId, $userId));
    }

    public function rehashPassword(Password $password, PasswordHashService $hashService): void
    {
        if (!$this->passwordIsValid($password, $hashService)) {
            return;
        }

        $this->recordThat(Event\IdentityPasswordWasRehashed::withNewHash(
            $this->identityId,
            $password->hash($hashService)
        ));
    }

    public function changePassword(
        Password $currentPassword,
        Password $newPassword,
        PasswordHashService $hashService
    ): void {
        if (!$this->passwordIsValid($currentPassword, $hashService)) {
            $this->recordThat(Event\IdentityPasswordChangeWasDenied::with(
                $this->identityId
            ));

            return;
        }

        $this->recordThat(Event\IdentityPasswordWasChanged::withNewHash(
            $this->identityId,
            $newPassword->hash($hashService)
        ));
    }

    public function identityId(): IdentityId
    {
        return $this->identityId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function passwordIsValid(Password $password, PasswordHashService $hashService): bool
    {
        return $password->isValid($this->passwordHash, $hashService);
    }

    public function passwordNeedsRehash(PasswordHashService $hashService): bool
    {
        return $this->passwordHash->needsRehash($hashService);
    }

    protected function aggregateId(): string
    {
        return $this->identityId->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (true) {
            case $event instanceof Event\IdentityWasCreatedForNewUser:
                $this->identityId = $event->identityId();
                $this->userId = $event->userId();
                $this->passwordHash = $event->passwordHash();
                break;
            case $event instanceof Event\IdentityPasswordWasRehashed:
                $this->passwordHash = $event->passwordHash();
                break;
            case $event instanceof Event\IdentityPasswordWasChanged:
                $this->passwordHash = $event->passwordHash();
                break;
        }
    }
}
