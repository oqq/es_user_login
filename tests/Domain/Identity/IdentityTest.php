<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity;

use Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordChangeWasDenied;
use Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordWasChanged;
use Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordWasRehashed;
use Oqq\EsUserLogin\Domain\Identity\Event\IdentityWasCreated;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use OqqTest\EsUserLogin\AggregateRootMockFactory;
use OqqTest\EsUserLogin\Domain\AggregateRootTestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Identity
 */
final class IdentityTest extends AggregateRootTestCase
{
    /**
     * @test
     */
    public function it_creates_identity_for_user_and_password(): Identity
    {
        $identityId = IdentityId::generate();
        $userId = UserId::generate();

        /** @var User $user */
        $user = AggregateRootMockFactory::create(User::class, [
           'userId' => $userId,
        ]);

        $password = Password::fromString('secret');
        $passwordHash = PasswordHash::fromString('hash');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->hash('secret')->willReturn($passwordHash);

        $identity = Identity::createForUserWithPassword($identityId, $user, $password, $hashService->reveal());

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertTrue($identity->identityId()->sameValueAs($identityId));
        $this->assertAggregateId($identityId->toString(), $identity);

        $events = $this->extractPendingEvents($identity);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(IdentityWasCreated::class, $events[0]);

        $expectedPayload = [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        $this->assertSame($identityId->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());

        $this->assertTrue($identity->userId()->sameValueAs($userId));

        $hashService->isValid('secret', $passwordHash)->willReturn(true);
        $this->assertTrue($identity->passwordIsValid($password, $hashService->reveal()));

        $hashService->needsRehash($passwordHash)->willReturn(true);
        $this->assertTrue($identity->passwordNeedsRehash($hashService->reveal()));

        return $identity;
    }

    /**
     * @test
     * @depends it_creates_identity_for_user_and_password
     */
    public function it_rehashes_password(Identity $identity): void
    {
        $password = Password::fromString('secret');
        $passwordHash = PasswordHash::fromString('hash');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('secret', $passwordHash)->willReturn(true);
        $hashService->hash('secret')->willReturn($passwordHash);

        $identity->rehashPassword($password, $hashService->reveal());

        $events = $this->extractPendingEvents($identity);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(IdentityPasswordWasRehashed::class, $events[0]);

        $expectedPayload = [
            'identity_id' => $identity->identityId()->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        $this->assertSame($identity->identityId()->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());
    }

    /**
     * @test
     * @depends it_creates_identity_for_user_and_password
     */
    public function it_does_not_rehash_if_password_is_not_valid(Identity $identity): void
    {
        $password = Password::fromString('secret');
        $passwordHash = PasswordHash::fromString('hash');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('secret', $passwordHash)->willReturn(false);
        $hashService->hash('secret')->shouldNotBeCalled();

        $identity->rehashPassword($password, $hashService->reveal());
    }

    /**
     * @test
     * @depends it_creates_identity_for_user_and_password
     */
    public function it_changes_password_with_valid_current_password(Identity $identity): void
    {
        $currentPassword = Password::fromString('old_secret');
        $newPassword = Password::fromString('new_secret');
        $passwordHash = PasswordHash::fromString('hash');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('old_secret', $passwordHash)->willReturn(true);
        $hashService->hash('new_secret')->willReturn($passwordHash);

        $identity->changePassword($currentPassword, $newPassword, $hashService->reveal());

        $events = $this->extractPendingEvents($identity);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(IdentityPasswordWasChanged::class, $events[0]);

        $expectedPayload = [
            'identity_id' => $identity->identityId()->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        $this->assertSame($identity->identityId()->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());
    }

    /**
     * @test
     * @depends it_creates_identity_for_user_and_password
     */
    public function it_denies_password_change_with_invalid_password(Identity $identity): void
    {
        $currentPassword = Password::fromString('old_secret');
        $newPassword = Password::fromString('new_secret');
        $passwordHash = PasswordHash::fromString('hash');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('old_secret', $passwordHash)->willReturn(false);

        $identity->changePassword($currentPassword, $newPassword, $hashService->reveal());

        $events = $this->extractPendingEvents($identity);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(IdentityPasswordChangeWasDenied::class, $events[0]);

        $expectedPayload = [
            'identity_id' => $identity->identityId()->toString(),
        ];

        $this->assertSame($identity->identityId()->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());
    }
}
