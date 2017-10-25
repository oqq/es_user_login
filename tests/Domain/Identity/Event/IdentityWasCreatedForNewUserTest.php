<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Event;

use Oqq\EsUserLogin\Domain\Identity\Event\IdentityWasCreatedForNewUser;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Event\IdentityWasCreatedForNewUser
 */
final class IdentityWasCreatedForNewUserTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_event(): void
    {
        $identityId = IdentityId::generate();
        $userId = UserId::generate();
        $passwordHash = PasswordHash::fromString('hash');

        $event = IdentityWasCreatedForNewUser::withUserIdAndPassword($identityId, $userId, $passwordHash);

        $this->assertInstanceOf(IdentityWasCreatedForNewUser::class, $event);
        $this->assertEquals($identityId->toString(), $event->aggregateId());

        $expectedPayload = [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        $this->assertSame($expectedPayload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
        $this->assertTrue($event->userId()->sameValueAs($userId));
        $this->assertSame($passwordHash->toString(), $event->passwordHash()->toString());
    }

    /**
     * @test
     */
    public function it_creates_event_from_payload(): void
    {
        $identityId = IdentityId::generate();
        $userId = UserId::generate();
        $passwordHash = PasswordHash::fromString('hash');

        $payload = [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        /** @var IdentityWasCreatedForNewUser $event */
        $event = IdentityWasCreatedForNewUser::occur($identityId->toString(), $payload);

        $this->assertSame($payload, $event->payload());
        $this->assertTrue($event->identityId()->sameValueAs($identityId));
        $this->assertTrue($event->userId()->sameValueAs($userId));
        $this->assertSame($passwordHash->toString(), $event->passwordHash()->toString());
    }
}
