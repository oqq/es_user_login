<?php

declare(strict_types = 1);

namespace OqqTest\EsUserLogin\Domain;

use Oqq\EsUserLogin\Domain\AggregateId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \Oqq\EsUserLogin\Domain\AggregateId
 */
abstract class AggregateIdTestCase extends TestCase
{
    /**
     * @test
     */
    public function it_generates_an_aggregate_id(): void
    {
        $aggregateId = $this->generateAggregateId();

        $this->assertInstanceOf(AggregateId::class, $aggregateId);
        $this->assertInstanceOf($this->getAggregateIdClassName(), $aggregateId);
    }

    /**
     * @test
     */
    public function it_creates_an_aggregate_id_from_string(): void
    {
        $uuid = Uuid::uuid4();
        $aggregateId = $this->aggregateIdFromString($uuid->toString());

        $this->assertInstanceOf(AggregateId::class, $aggregateId);
        $this->assertInstanceOf($this->getAggregateIdClassName(), $aggregateId);
    }

    /**
     * @test
     */
    public function it_returns_an_aggregate_id_as_string(): void
    {
        $uuid = Uuid::uuid4();
        $aggregateId = $this->aggregateIdFromString($uuid->toString());

        $this->assertInternalType('string', $aggregateId->toString());
        $this->assertEquals($uuid->toString(), $aggregateId->toString());
    }

    /**
     * @test
     * @dataProvider aggregateIdMatches
     */
    public function it_returns_expected_result_on_comparison(
        AggregateId $aggregateId,
        AggregateId $otherAggregateId,
        bool $expectedResult
    ): void {
        $this->assertEquals($expectedResult, $aggregateId->sameValueAs($otherAggregateId));
    }

    public function aggregateIdMatches(): array
    {
        $uuid = Uuid::uuid4();
        $aggregateId = $this->aggregateIdFromString($uuid->toString());

        return [
            [$aggregateId, $aggregateId, true],
            [$aggregateId, $this->aggregateIdFromString($uuid->toString()), true],

            [$aggregateId, $this->generateAggregateId(), false],
            [$aggregateId, AggregateIdMock::fromString($uuid->toString()), false],
            [$aggregateId, AggregateIdMock::generate(), false],
        ];
    }

    abstract protected function getAggregateIdClassName(): string;

    private function generateAggregateId(): AggregateId
    {
        /** @var AggregateId $aggregateIdName */
        $aggregateIdName = $this->getAggregateIdClassName();

        return $aggregateIdName::generate();
    }

    private function aggregateIdFromString(string $value): AggregateId
    {
        /** @var AggregateId $aggregateIdName */
        $aggregateIdName = $this->getAggregateIdClassName();

        return $aggregateIdName::fromString($value);
    }
}
