<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity;

interface IdentityRepository
{
    public function load(IdentityId $identityId): ?Identity;

    public function save(Identity $identity): void;
}
