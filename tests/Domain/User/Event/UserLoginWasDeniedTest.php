<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Event;

use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\User\Event\UserLoginWasDenied;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Event\UserLoginWasDenied
 */
final class UserLoginWasDeniedTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $userId = UserId::generate();
        $identityId = IdentityId::generate();

        $event = UserLoginWasDenied::withIdentity($userId, $identityId);

        $this->assertInstanceOf(UserLoginWasDenied::class, $event);
        $this->assertEquals($userId->toString(), $event->aggregateId());

        $expectedPayload = [
            'user_id' => $userId->toString(),
            'identity_id' => $identityId->toString(),
        ];

        $this->assertSame($expectedPayload, $event->payload());
        $this->assertTrue($event->userId()->sameValueAs($userId));
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
    }

    /**
     * @test
     */
    public function it_creates_event_from_payload(): void
    {
        $userId = UserId::generate();
        $identityId = IdentityId::generate();

        $payload = [
            'user_id' => $userId->toString(),
            'identity_id' => $identityId->toString(),
        ];

        /** @var UserLoginWasDenied $event */
        $event = UserLoginWasDenied::occur($userId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->userId()->sameValueAs($userId));
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
    }
}
