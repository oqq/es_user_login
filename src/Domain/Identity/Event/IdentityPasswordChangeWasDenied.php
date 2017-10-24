<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\AggregateChanged;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;

final class IdentityPasswordChangeWasDenied extends AggregateChanged
{
    /** @var IdentityId */
    private $identityId;

    public static function with(IdentityId $identityId): self
    {
        /** @var static $event */
        $event = self::occur($identityId->toString(), [
            'identity_id' => $identityId->toString(),
        ]);

        $event->identityId = $identityId;

        return $event;
    }

    public function identityId(): IdentityId
    {
        if (null === $this->identityId) {
            $this->identityId = IdentityId::fromString($this->payload['identity_id']);
        }

        return $this->identityId;
    }
}
