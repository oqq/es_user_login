<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\Identity\Event\IdentityWasReusedForNewUser;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Event\IdentityWasReusedForNewUser
 */
final class IdentityWasReusedForNewUserTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $identityId = IdentityId::generate();
        $userId = UserId::generate();

        $event = IdentityWasReusedForNewUser::withUserId($identityId, $userId);

        $this->assertInstanceOf(IdentityWasReusedForNewUser::class, $event);
        $this->assertEquals($identityId->toString(), $event->aggregateId());

        $expectedPayload = [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
        ];

        $this->assertSame($expectedPayload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
        $this->assertTrue($event->userId()->sameValueAs($userId));
    }

    /**
     * @test
     */
    public function it_creates_event_from_payload(): void
    {
        $identityId = IdentityId::generate();
        $userId = UserId::generate();

        $payload = [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
        ];

        /** @var IdentityWasReusedForNewUser $event */
        $event = IdentityWasReusedForNewUser::occur($identityId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
        $this->assertTrue($event->userId()->sameValueAs($userId));
    }
}
