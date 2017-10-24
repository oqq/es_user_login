<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Event;

use Oqq\EsUserLogin\Domain\User\Event\UserLoggedOut;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Event\UserLoggedOut
 */
final class UserLoggedOutTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $userId = UserId::generate();

        $event = UserLoggedOut::with($userId);

        $this->assertInstanceOf(UserLoggedOut::class, $event);
        $this->assertEquals($userId->toString(), $event->aggregateId());

        $expectedPayload = [
            'user_id' => $userId->toString(),
        ];

        $this->assertSame($expectedPayload, $event->payload());
        $this->assertTrue($event->userId()->sameValueAs($userId));
    }

    /**
     * @test
     */
    public function it_creates_event_from_payload(): void
    {
        $userId = UserId::generate();

        $payload = [
            'user_id' => $userId->toString(),
        ];

        /** @var UserLoggedOut $event */
        $event = UserLoggedOut::occur($userId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->userId()->sameValueAs($userId));
    }
}
