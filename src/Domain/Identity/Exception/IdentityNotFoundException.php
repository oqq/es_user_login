<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Exception;

use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Exception\DomainException;
use Oqq\EsUserLogin\Exception\RuntimeException;

final class IdentityNotFoundException extends RuntimeException implements DomainException
{
    public static function withIdentityId(IdentityId $identityId): self
    {
        return new self(sprintf(
            'Identity with id "%s" was not found!',
            $identityId->toString()
        ));
    }
}
