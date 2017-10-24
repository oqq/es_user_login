<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Exception;

use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Exception\DomainException;
use Oqq\EsUserLogin\Exception\RuntimeException;

final class UserNotFoundException extends RuntimeException implements DomainException
{
    public static function withUserId(UserId $userId): self
    {
        return new self(sprintf(
            'User with id "%s" was not found!',
            $userId->toString()
        ));
    }
}
