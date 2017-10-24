<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain;

use Assert\Assertion;

final class Password
{
    private $value;

    public static function fromString(string $value): self
    {
        Assertion::notEmpty($value);

        return new self($value);
    }

    public function __debugInfo(): array
    {
        return [
            'value' => '***********',
        ];
    }

    public function hash(PasswordHashService $hashService): PasswordHash
    {
        return $hashService->hash($this->value);
    }

    public function isValid(PasswordHash $hash, PasswordHashService $hashService): bool
    {
        return $hashService->isValid($this->value, $hash);
    }

    private function __construct(string $value)
    {
        $this->value = $value;
    }
}
