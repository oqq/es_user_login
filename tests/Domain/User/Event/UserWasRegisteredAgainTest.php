<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Event;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\User\Event\UserWasRegisteredAgain;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Event\UserWasRegisteredAgain
 */
final class UserWasRegisteredAgainTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $userId = UserId::generate();
        $emailAddress = EmailAddress::fromString('foo@bar.com');

        $event = UserWasRegisteredAgain::withEmailAddress($userId, $emailAddress);

        $this->assertInstanceOf(UserWasRegisteredAgain::class, $event);
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

        /** @var UserWasRegisteredAgain $event */
        $event = UserWasRegisteredAgain::occur($userId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->userId()->sameValueAs($userId));
        $this->assertSame($emailAddress->toString(), $event->emailAddress()->toString());
    }
}
