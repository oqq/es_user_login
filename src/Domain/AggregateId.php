<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class AggregateId
{
    protected const NIL = Uuid::NIL;

    private $uuid;

    /**
     * @return static
     */
    final public static function fromString(string $aggregateId): self
    {
        return new static(Uuid::fromString($aggregateId));
    }

    /**
     * @return static
     */
    final public static function generate(): self
    {
        return new static(Uuid::uuid4());
    }

    final public function __toString(): string
    {
        return $this->toString();
    }

    final public function toString(): string
    {
        return $this->uuid->toString();
    }

    final public function sameValueAs(AggregateId $aggregateId): bool
    {
        return $this->uuid->toString() === $aggregateId->toString()
            && \get_called_class() === \get_class($aggregateId);
    }

    final protected function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }
}
