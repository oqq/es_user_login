<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain;

use Assert\Assertion;

final class EmailAddress
{
    private $value;

    public static function fromString(string $email): self
    {
        Assertion::email($email);

        return new self($email);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function normalized(): string
    {
        return \mb_strtolower($this->value);
    }

    public function sameAs(EmailAddress $emailAddress): bool
    {
        return $this->normalized() === $emailAddress->normalized();
    }

    private function __construct(string $value)
    {
        $this->value = $value;
    }
}
