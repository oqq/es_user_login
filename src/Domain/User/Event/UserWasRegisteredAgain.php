<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Event;

use Oqq\EsUserLogin\Domain\AggregateChanged;
use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\User\UserId;

final class UserWasRegisteredAgain extends AggregateChanged
{
    /** @var UserId */
    private $userId;

    /** @var EmailAddress */
    private $emailAddress;

    public static function withEmailAddress(UserId $userId, EmailAddress $email): self
    {
        /** @var static $event */
        $event = self::occur($userId->toString(), [
            'user_id' => $userId->toString(),
            'email_address' => $email->toString(),
        ]);

        $event->userId = $userId;
        $event->emailAddress = $email;

        return $event;
    }

    public function userId(): UserId
    {
        if (null === $this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function emailAddress(): EmailAddress
    {
        if (null === $this->emailAddress) {
            $this->emailAddress = EmailAddress::fromString($this->payload['email_address']);
        }

        return $this->emailAddress;
    }
}
