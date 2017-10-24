<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Event;

use Oqq\EsUserLogin\Domain\AggregateChanged;
use Oqq\EsUserLogin\Domain\User\UserId;

final class UserLoggedOut extends AggregateChanged
{
    /** @var UserId */
    private $userId;

    public static function with(UserId $userId): self
    {
        /** @var static $event */
        $event = self::occur($userId->toString(), [
            'user_id' => $userId->toString(),
        ]);

        $event->userId = $userId;

        return $event;
    }

    public function userId(): UserId
    {
        if (null === $this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
