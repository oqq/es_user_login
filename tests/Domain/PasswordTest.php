<?php

declare(strict_types = 1);

namespace OqqTest\EsUserLogin\Domain;

use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\Password
 */
final class PasswordTest extends TestCase
{
    /**
     * @test
     * @dataProvider validValues
     */
    public function it_creates_from_string_with_valid_contents(string $value): void
    {
        $password = Password::fromString($value);

        $this->assertInstanceOf(Password::class, $password);
    }

    public function validValues(): array
    {
        return [
            ['test'],
        ];
    }

    /**
     * @test
     * @coversNothing
     * @dataProvider invalidValues
     */
    public function it_throws_exception_with_invalid_value($value): void
    {
        $this->expectException(\Throwable::class);

        Password::fromString($value);
    }

    public function invalidValues(): array
    {
        return [
            [''],
            [2],
            [-2],
            [null],
            [1.123],
            [[]],
        ];
    }

    /**
     * @test
     */
    public function it_creates_password_hash(): void
    {
        $password = Password::fromString('password');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->hash('password')->willReturn(PasswordHash::fromString('hash'));

        $passwordHash = $password->hash($hashService->reveal());

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
        $this->assertSame('hash', $passwordHash->toString());
    }

    /**
     * @test
     */
    public function it_hides_value_on_debug_output(): void
    {
        $password = Password::fromString('secret');

        ob_start();
        var_dump($password);
        $output = ob_get_clean();

        $this->assertNotContains('secret', $output);
    }

    /**
     * @test
     */
    public function it_returns_validity(): void
    {
        $passwordHash = PasswordHash::fromString('password');

        $validPassword = Password::fromString('password');
        $invalidPassword = Password::fromString('invalid_password');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->isValid('password', $passwordHash)->willReturn(true);
        $hashService->isValid('invalid_password', $passwordHash)->willReturn(false);

        $this->assertTrue($validPassword->isValid($passwordHash, $hashService->reveal()));
        $this->assertFalse($invalidPassword->isValid($passwordHash, $hashService->reveal()));
    }
}
