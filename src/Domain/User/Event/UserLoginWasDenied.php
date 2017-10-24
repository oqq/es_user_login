<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Event;

use Oqq\EsUserLogin\Domain\AggregateChanged;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\User\UserId;

final class UserLoginWasDenied extends AggregateChanged
{
    /** @var UserId */
    private $userId;

    /** @var IdentityId */
    private $identityId;

    public static function withIdentity(UserId $userId, IdentityId $identityId): self
    {
        /** @var static $event */
        $event = self::occur($userId->toString(), [
            'user_id' => $userId->toString(),
            'identity_id' => $identityId->toString(),
        ]);

        $event->userId = $userId;
        $event->identityId = $identityId;

        return $event;
    }

    public function userId(): UserId
    {
        if (null === $this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function identityId(): IdentityId
    {
        if (null === $this->identityId) {
            $this->identityId = IdentityId::fromString($this->payload['identity_id']);
        }

        return $this->identityId;
    }
}
