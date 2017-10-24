<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Command;

use Assert\Assertion;
use Oqq\EsUserLogin\Domain\Command;
use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Password;

final class Login extends Command
{
    /** @var string */
    private $emailAddress;

    /** @var string */
    private $password;

    public function emailAddress(): EmailAddress
    {
        return EmailAddress::fromString($this->emailAddress);
    }

    public function password(): Password
    {
        return Password::fromString($this->password);
    }

    public function payload(): array
    {
        return [
            'email_address' => $this->emailAddress,
            'password' => $this->password,
        ];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::choicesNotEmpty($payload, ['email_address', 'password']);

        $this->emailAddress = $payload['email_address'];
        $this->password = $payload['password'];
    }
}
