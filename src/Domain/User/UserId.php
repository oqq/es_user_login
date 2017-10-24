<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User;

use Oqq\EsUserLogin\Domain\AggregateId;

final class UserId extends AggregateId
{
    public const GUEST_ID = self::NIL;
}
