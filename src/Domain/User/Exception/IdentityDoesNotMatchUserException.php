<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User\Exception;

use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Exception\DomainException;
use Oqq\EsUserLogin\Exception\RuntimeException;

final class IdentityDoesNotMatchUserException extends RuntimeException implements DomainException
{
    public static function withIdentityAndUserId(IdentityId $identityId, UserId $userId): self
    {
        return new self(sprintf(
            'The identity with id "%s" does not match to user with id "%s"',
            $identityId->toString(),
            $userId->toString()
        ));
    }
}
