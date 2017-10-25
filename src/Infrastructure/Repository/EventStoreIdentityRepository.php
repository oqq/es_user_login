<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Infrastructure\Repository;

use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

final class EventStoreIdentityRepository extends AggregateRepository implements IdentityRepository
{
    public function load(IdentityId $identityId): ?Identity
    {
        return $this->getAggregateRoot($identityId->toString());
    }

    public function save(Identity $identity): void
    {
        $this->saveAggregateRoot($identity);
    }
}
