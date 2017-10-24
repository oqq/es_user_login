<?php

declare(strict_types = 1);

namespace OqqTest\EsUserLogin\Domain\User;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\Event\IdentityPasswordWasRehashed;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\Event\UserLoggedIn;
use Oqq\EsUserLogin\Domain\User\Event\UserLoginWasDenied;
use Oqq\EsUserLogin\Domain\User\Event\UserWasRegistered;
use Oqq\EsUserLogin\Domain\User\Event\UserWasRegisteredAgain;
use Oqq\EsUserLogin\Domain\User\Exception\IdentityDoesNotMatchUserException;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use OqqTest\EsUserLogin\AggregateRootMockFactory;
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

    /**
     * @test
     * @depends it_registers_an_user
     */
    public function it_login_with_valid_password(User $user): void
    {
        $identityId = IdentityId::generate();
        $password = Password::fromString('secure');
        $passwordHash = PasswordHash::fromString('hash');

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'userId' => $user->userId(),
            'passwordHash' => $passwordHash,
        ]);

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('secure', $passwordHash)->willReturn(true);
        $hashService->needsRehash($passwordHash)->willReturn(false);

        $user->loginWithIdentity($identity, $password, $hashService->reveal());

        $events = $this->extractPendingEvents($user);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(UserLoggedIn::class, $events[0]);

        $expectedPayload = [
            'user_id' => $user->userId()->toString(),
            'identity_id' => $identityId->toString(),
        ];

        $this->assertSame($user->userId()->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());
    }

    /**
     * @test
     * @depends it_registers_an_user
     */
    public function it_denies_login_with_wrong_password(User $user): void
    {
        $identityId = IdentityId::generate();
        $password = Password::fromString('wrong');
        $passwordHash = PasswordHash::fromString('hash');

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'userId' => $user->userId(),
            'passwordHash' => $passwordHash,
        ]);

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('wrong', $passwordHash)->willReturn(false);

        $user->loginWithIdentity($identity, $password, $hashService->reveal());

        $events = $this->extractPendingEvents($user);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(UserLoginWasDenied::class, $events[0]);

        $expectedPayload = [
            'user_id' => $user->userId()->toString(),
            'identity_id' => $identityId->toString(),
        ];

        $this->assertSame($user->userId()->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());
    }

    /**
     * @test
     * @depends it_registers_an_user
     */
    public function it_rehashes_password_if_required(User $user): void
    {
        $identityId = IdentityId::generate();
        $password = Password::fromString('secure');
        $passwordHash = PasswordHash::fromString('hash');

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'userId' => $user->userId(),
            'passwordHash' => $passwordHash,
        ]);

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('secure', $passwordHash)->willReturn(true);
        $hashService->needsRehash($passwordHash)->willReturn(true);
        $hashService->hash('secure')->willReturn($passwordHash)->shouldBeCalled();

        $user->loginWithIdentity($identity, $password, $hashService->reveal());

        $events = $this->extractPendingEvents($identity);
        $this->assertCount(1, $events);

        $this->assertInstanceOf(IdentityPasswordWasRehashed::class, $events[0]);

        $expectedPayload = [
            'identity_id' => $identityId->toString(),
            'password_hash' => $passwordHash->toString(),
        ];

        $this->assertSame($identityId->toString(), $events[0]->aggregateId());
        $this->assertSame($expectedPayload, $events[0]->payload());
    }

    /**
     * @test
     * @depends it_registers_an_user
     */
    public function it_throws_exception_with_wrong_identity(User $user): void
    {
        $this->expectException(IdentityDoesNotMatchUserException::class);

        $identityId = IdentityId::generate();
        $password = Password::fromString('secure');

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'userId' => UserId::generate(),
        ]);

        $hashService = $this->prophesize(PasswordHashService::class);

        $user->loginWithIdentity($identity, $password, $hashService->reveal());
    }
}
