<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Command;

use Assert\Assertion;
use Oqq\EsUserLogin\Domain\Command;
use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\User\UserId;

final class CreateUser extends Command
{
    /** @var string */
    private $userId;

    /** @var string */
    private $emailAddress;

    public function userId(): UserId
    {
        return UserId::fromString($this->userId);
    }

    public function emailAddress(): EmailAddress
    {
        return EmailAddress::fromString($this->emailAddress);
    }

    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
            'email_address' => $this->emailAddress,
        ];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::choicesNotEmpty($payload, ['user_id', 'email_address']);

        $this->userId = $payload['user_id'];
        $this->emailAddress = $payload['email_address'];
    }
}
