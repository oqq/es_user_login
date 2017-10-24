<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain;

use Assert\Assertion;

final class PasswordHash
{
    private $value;

    public static function fromString(string $passwordHashString): self
    {
        Assertion::notEmpty($passwordHashString);

        return new self($passwordHashString);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function needsRehash(PasswordHashService $hashService): bool
    {
        return $hashService->needsRehash($this);
    }

    private function __construct(string $value)
    {
        $this->value = $value;
    }
}
