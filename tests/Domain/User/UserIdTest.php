<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User;

use Oqq\EsUserLogin\Domain\User\UserId;
use OqqTest\EsUserLogin\Domain\AggregateIdTestCase;

class UserIdTest extends AggregateIdTestCase
{
    protected function getAggregateIdClassName(): string
    {
        return UserId::class;
    }
}
