<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity;

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
    public function it_creates_identity_for_user_and_password(): void
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
    }
}
