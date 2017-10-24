<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\AggregateChanged;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\User\UserId;

final class IdentityWasCreated extends AggregateChanged
{
    /** @var IdentityId */
    private $identityId;

    /** @var UserId */
    private $userId;

    /** @var PasswordHash */
    private $passwordHash;

    public static function forUser(IdentityId $identityId, UserId $userId, PasswordHash $passwordHash): self
    {
        /** @var static $event */
        $event = self::occur($identityId->toString(), [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
            'password_hash' => $passwordHash->toString(),
        ]);

        $event->identityId = $identityId;
        $event->userId = $userId;
        $event->passwordHash = $passwordHash;

        return $event;
    }

    public function identityId(): IdentityId
    {
        if (null === $this->identityId) {
            $this->identityId = IdentityId::fromString($this->payload['identity_id']);
        }

        return $this->identityId;
    }

    public function userId(): UserId
    {
        if (null === $this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function passwordHash(): PasswordHash
    {
        if (null === $this->passwordHash) {
            $this->passwordHash = PasswordHash::fromString($this->payload['password_hash']);
        }

        return $this->passwordHash;
    }
}
