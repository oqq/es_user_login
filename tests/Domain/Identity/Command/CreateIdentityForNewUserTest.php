<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Command;

use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\Identity\Command\CreateIdentityForNewUser;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Command\CreateIdentityForNewUser
 */
final class CreateIdentityForNewUserTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_command_from_payload(): void
    {
        $identityId = IdentityId::generate();
        $userId = UserId::generate();
        $password = Password::fromString('secret');

        $payload = [
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
        ];

        $command = CreateIdentityForNewUser::withPassword($identityId, $userId, $password);

        $this->assertEquals($payload, $command->payload());

        $this->assertInstanceOf(IdentityId::class, $command->identityId());
        $this->assertTrue($command->identityId()->sameValueAs($identityId));

        $this->assertInstanceOf(UserId::class, $command->userId());
        $this->assertTrue($command->userId()->sameValueAs($userId));

        $this->assertInstanceOf(Password::class, $command->password());
    }
}
