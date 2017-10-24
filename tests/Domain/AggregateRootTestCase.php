<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain;

use PHPUnit\Framework\TestCase;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;

abstract class AggregateRootTestCase extends TestCase
{
    /** @var AggregateTranslator */
    private $aggregateTranslator;

    /**
     * @return AggregateChanged[]
     */
    protected function extractPendingEvents(AggregateRoot $aggregateRoot): array
    {
        return $this->aggregateTranslator()->extractPendingStreamEvents($aggregateRoot);
    }

    protected function assertAggregateId(string $expected, AggregateRoot $aggregateRoot): void
    {
       $this->assertSame($expected, $this->aggregateTranslator()->extractAggregateId($aggregateRoot));
    }

    private function aggregateTranslator(): AggregateTranslator
    {
        if (null === $this->aggregateTranslator) {
            $this->aggregateTranslator = new AggregateTranslator();
        }

        return $this->aggregateTranslator;
    }
}
