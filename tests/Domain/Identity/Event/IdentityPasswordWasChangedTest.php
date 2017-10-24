<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordWasChanged;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\PasswordHash;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordWasChanged
 */
final class IdentityPasswordWasChangedTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $identityId = IdentityId::generate();
        $passwordHash = PasswordHash::fromString('hash');

        $event = IdentityPasswordWasChanged::withNewHash($identityId, $passwordHash);

        $this->assertInstanceOf(IdentityPasswordWasChanged::class, $event);
        $this->assertEquals($identityId->toString(), $event->aggregateId());

        $expectedPayload = [
            'identity_id' => $identityId->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        $this->assertSame($expectedPayload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
        $this->assertSame($passwordHash->toString(), $event->passwordHash()->toString());
    }

    /**
     * @test
     */
    public function it_creates_event_from_payload(): void
    {
        $identityId = IdentityId::generate();
        $passwordHash = PasswordHash::fromString('hash');

        $payload = [
            'identity_id' => $identityId->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        /** @var IdentityPasswordWasChanged $event */
        $event = IdentityPasswordWasChanged::occur($identityId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
        $this->assertSame($passwordHash->toString(), $event->passwordHash()->toString());
    }
}
