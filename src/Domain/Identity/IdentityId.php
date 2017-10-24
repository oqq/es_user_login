<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity;

use Oqq\EsUserLogin\Domain\AggregateId;
use Oqq\EsUserLogin\Domain\EmailAddress;
use Ramsey\Uuid\Uuid;

final class IdentityId extends AggregateId
{
    public static function fromEmailAddress(EmailAddress $emailAddress): self
    {
        return new self(Uuid::uuid5(Uuid::NAMESPACE_URL, 'mailto:' . $emailAddress->toString()));
    }
}
