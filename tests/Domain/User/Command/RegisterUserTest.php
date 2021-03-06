<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Command;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\User\Command\RegisterUser;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Command\RegisterUser
 */
final class RegisterUserTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_command_from_payload(): void
    {
        $userId = UserId::generate();

        $payload = [
            'user_id' => $userId->toString(),
            'email_address' => 'user@test.com',
            'password' => 'secure',
        ];

        $command = new RegisterUser($payload);

        $this->assertEquals($payload, $command->payload());

        $this->assertInstanceOf(UserId::class, $command->userId());
        $this->assertTrue($command->userId()->sameValueAs($userId));

        $this->assertInstanceOf(EmailAddress::class, $command->emailAddress());
        $this->assertSame('user@test.com', $command->emailAddress()->toString());

        $this->assertInstanceOf(Password::class, $command->password());
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_user_id(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'user_id' => 'fuu',
            'email_address' => 'user@test.com',
            'password' => 'secure',

        ];

        $command = new RegisterUser($payload);
        $command->userId();
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_email_address(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'user_id' => UserId::generate()->toString(),
            'email_address' => 'invalid',
            'password' => 'secure',
        ];

        $command = new RegisterUser($payload);
        $command->emailAddress();
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_password(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'user_id' => UserId::generate()->toString(),
            'email_address' => 'user@test.com',
            'password' => '',
        ];

        $command = new RegisterUser($payload);
        $command->password();
    }

    /**
     * @test
     * @coversNothing
     * @dataProvider invalidPayload
     */
    public function it_throws_exception_with_invalid_payload(array $invalidPayload): void
    {
        $this->expectException(\Throwable::class);

        new RegisterUser($invalidPayload);
    }

    public function invalidPayload(): \Generator
    {
        $validValues = [
            'user_id' => UserId::generate()->toString(),
            'email_address' => 'user@test.com',
            'password' => 'secure',
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
