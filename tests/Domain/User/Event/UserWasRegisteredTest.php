<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Event;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\User\Event\UserWasRegistered;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Event\UserWasRegistered
 */
final class UserWasRegisteredTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $userId = UserId::generate();
        $emailAddress = EmailAddress::fromString('foo@bar.com');

        $event = UserWasRegistered::withEmailAddress($userId, $emailAddress);

        $this->assertInstanceOf(UserWasRegistered::class, $event);
        $this->assertEquals($userId->toString(), $event->aggregateId());

        $expectedPayload = [
            'user_id' => $userId->toString(),
            'email_address' => $emailAddress->toString(),
        ];

        $this->assertSame($expectedPayload, $event->payload());
        $this->assertTrue($event->userId()->sameValueAs($userId));
        $this->assertSame($emailAddress->toString(), $event->emailAddress()->toString());
    }

    /**
     * @test
     */
    public function it_creates_event_from_payload(): void
    {
        $userId = UserId::generate();
        $emailAddress = EmailAddress::fromString('foo@bar.com');

        $payload = [
            'user_id' => $userId->toString(),
            'email_address' => $emailAddress->toString(),
        ];

        /** @var UserWasRegistered $event */
        $event = UserWasRegistered::occur($userId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->userId()->sameValueAs($userId));
        $this->assertSame($emailAddress->toString(), $event->emailAddress()->toString());
    }
}
