<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Command;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\User\Command\Login;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Command\Login
 */
final class LoginTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_command_from_payload(): void
    {
        $payload = [
            'email_address' => 'user@test.com',
            'password' => 'secure',
        ];

        $command = new Login($payload);

        $this->assertEquals($payload, $command->payload());

        $this->assertInstanceOf(EmailAddress::class, $command->emailAddress());
        $this->assertSame('user@test.com', $command->emailAddress()->toString());

        $this->assertInstanceOf(Password::class, $command->password());
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_email_address(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'email_address' => 'invalid',
            'password' => 'secure',
        ];

        $command = new Login($payload);
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
            'email_address' => 'user@test.com',
            'password' => '',
        ];

        $command = new Login($payload);
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

        new Login($invalidPayload);
    }

    public function invalidPayload(): \Generator
    {
        $validValues = [
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
