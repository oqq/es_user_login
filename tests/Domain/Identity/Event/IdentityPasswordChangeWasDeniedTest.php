<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordChangeWasDenied;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordChangeWasDenied
 */
final class IdentityPasswordChangeWasDeniedTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $identityId = IdentityId::generate();

        $event = IdentityPasswordChangeWasDenied::with($identityId);

        $this->assertInstanceOf(IdentityPasswordChangeWasDenied::class, $event);
        $this->assertEquals($identityId->toString(), $event->aggregateId());

        $expectedPayload = [
            'identity_id' => $identityId->toString(),
        ];

        $this->assertSame($expectedPayload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
    }

    /**
     * @test
     */
    public function it_creates_event_from_payload(): void
    {
        $identityId = IdentityId::generate();

        $payload = [
            'identity_id' => $identityId->toString(),
        ];

        /** @var IdentityPasswordChangeWasDenied $event */
        $event = IdentityPasswordChangeWasDenied::occur($identityId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
    }
}
