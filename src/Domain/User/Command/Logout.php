<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Command;

use Assert\Assertion;
use Oqq\EsUserLogin\Domain\Command;
use Oqq\EsUserLogin\Domain\User\UserId;

final class Logout extends Command
{
    /** @var string */
    private $userId;

    public function userId(): UserId
    {
        return UserId::fromString($this->userId);
    }

    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
        ];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::choicesNotEmpty($payload, ['user_id']);

        $this->userId = $payload['user_id'];
    }
}
