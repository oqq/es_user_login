<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity;

use Oqq\EsUserLogin\Domain\AggregateRoot;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\User;
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

    public static function createForUserWithPassword(
        IdentityId $identityId,
        User $user,
        Password $password,
        PasswordHashService $hashService
    ): self {
        $identity = new self();

        $identity->recordThat(Event\IdentityWasCreated::forUser(
            $identityId,
            $user->userId(),
            $password->hash($hashService)
        ));

        return $identity;
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
            case $event instanceof Event\IdentityWasCreated:
                $this->identityId = $event->identityId();
                $this->userId = $event->userId();
                $this->passwordHash = $event->passwordHash();
                break;
            case $event instanceof Event\IdentityPasswordWasRehashed:
                $this->passwordHash = $event->passwordHash();
                break;
        }
    }
}
