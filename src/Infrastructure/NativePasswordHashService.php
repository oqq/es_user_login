<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Infrastructure;

use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;

final class NativePasswordHashService implements PasswordHashService
{
    public function hash(string $password): PasswordHash
    {
        return PasswordHash::fromString(password_hash($password, \PASSWORD_DEFAULT));
    }

    public function isValid(string $password, PasswordHash $passwordHash): bool
    {
       return password_verify($password, $passwordHash->toString());
    }

    public function needsRehash(PasswordHash $passwordHash): bool
    {
        return password_needs_rehash($passwordHash->toString(), \PASSWORD_DEFAULT);
    }
}
