<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\AggregateChanged;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\User\UserId;

final class IdentityWasReusedForNewUser extends AggregateChanged
{
    /** @var IdentityId */
    private $identityId;

    /** @var UserId */
    private $userId;

    public static function withUserId(IdentityId $identityId, UserId $userId): self
    {
        /** @var static $event */
        $event = self::occur($identityId->toString(), [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
        ]);

        $event->identityId = $identityId;
        $event->userId = $userId;

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
}
