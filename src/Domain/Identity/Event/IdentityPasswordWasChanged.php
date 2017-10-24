<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\AggregateChanged;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\PasswordHash;

final class IdentityPasswordWasChanged extends AggregateChanged
{
    /** @var IdentityId */
    private $identityId;

    /** @var PasswordHash */
    private $passwordHash;

    public static function withNewHash(IdentityId $identityId, PasswordHash $passwordHash): self
    {
        /** @var static $event */
        $event = self::occur($identityId->toString(), [
            'identity_id' => $identityId->toString(),
            'password_hash' => $passwordHash->toString(),
        ]);

        $event->identityId = $identityId;
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

    public function passwordHash(): PasswordHash
    {
        if (null === $this->passwordHash) {
            $this->passwordHash = PasswordHash::fromString($this->payload['password_hash']);
        }

        return $this->passwordHash;
    }
}
