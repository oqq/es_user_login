<?php

declare(strict_types = 1);

namespace OqqTest\EsUserLogin\Domain\User;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\User\Event\UserWasRegistered;
use Oqq\EsUserLogin\Domain\User\Event\UserWasRegisteredAgain;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use OqqTest\EsUserLogin\Domain\AggregateRootTestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\User
 */
final class UserTest extends AggregateRootTestCase
{
    /**
     * @test
     */
    public function it_registers_an_user(): User
    {
        $userId = UserId::generate();
        $emailAddress = EmailAddress::fromString('test@burgeins.de');

        $user = User::register($userId, $emailAddress);
        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->userId()->sameValueAs($userId));
        $this->assertAggregateId($userId->toString(), $user);

        $events = $this->extractPendingEvents($user);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(UserWasRegistered::class, $events[0]);

        $expectedPayload = [
            'user_id' => $userId->toString(),
            'email_address' => $emailAddress->toString(),
        ];

        $this->assertSame($userId->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());

        return $user;
    }

    /**
     * @test
     * @depends it_registers_an_user
     */
    public function it_recognizes_another_register_attempt(User $user): void
    {
        $emailAddress = EmailAddress::fromString('test@burgeins.de');

        $user->registerAgain($emailAddress);

        $events = $this->extractPendingEvents($user);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(UserWasRegisteredAgain::class, $events[0]);

        $expectedPayload = [
            'user_id' => $user->userId()->toString(),
            'email_address' => $emailAddress->toString(),
        ];

        $this->assertSame($user->userId()->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());
    }
}
