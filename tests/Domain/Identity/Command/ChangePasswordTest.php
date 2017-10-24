<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Command;

use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\Identity\Command\ChangePassword;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Command\ChangePassword
 */
final class ChangePasswordTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_command_from_payload(): void
    {
        $identityId = IdentityId::generate();

        $payload = [
            'identity_id' => $identityId->toString(),
            'current_password' => 'current_password',
            'new_password' => 'new_password',
        ];

        $command = new ChangePassword($payload);

        $this->assertEquals($payload, $command->payload());

        $this->assertInstanceOf(IdentityId::class, $command->identityId());
        $this->assertTrue($command->identityId()->sameValueAs($identityId));

        $this->assertInstanceOf(Password::class, $command->currentPassword());
        $this->assertInstanceOf(Password::class, $command->newPassword());
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_identity_id(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'identity_id' => 'fuu',
            'current_password' => 'current_password',
            'new_password' => 'new_password',

        ];

        $command = new ChangePassword($payload);
        $command->identityId();
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_current_password(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'identity_id' => IdentityId::generate()->toString(),
            'current_password' => '',
            'new_password' => 'new_password',
        ];

        $command = new ChangePassword($payload);
        $command->currentPassword();
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_new_password(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'identity_id' => IdentityId::generate()->toString(),
            'current_password' => 'current_password',
            'new_password' => '',
        ];

        $command = new ChangePassword($payload);
        $command->newPassword();
    }

    /**
     * @test
     * @coversNothing
     * @dataProvider invalidPayload
     */
    public function it_throws_exception_with_invalid_payload(array $invalidPayload): void
    {
        $this->expectException(\Throwable::class);

        new ChangePassword($invalidPayload);
    }

    public function invalidPayload(): \Generator
    {
        $validValues = [
            'identity_id' => IdentityId::generate()->toString(),
            'current_password' => 'current_password',
            'new_password' => 'new_password',
        ];

        yield [[]];
        yield [['fuu' => 'bar']];

        foreach ($validValues as $key => $data) {
            $invalidValues = $validValues;

            $invalidValues[$key] = '';
            yield [$invalidValues];

            unset($invalidValues[$key]);
            yield [$invalidValues];
        }
    }
}
