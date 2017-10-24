<?php

declare(strict_types = 1);

namespace OqqTest\EsUserLogin\Domain;

use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\PasswordHash
 */
final class PasswordHashTest extends TestCase
{
    /**
     * @test
     * @dataProvider validValues
     */
    public function it_creates_from_string_with_valid_contents(string $value): void
    {
        $passwordHash = PasswordHash::fromString($value);

        $this->assertInstanceOf(PasswordHash::class, $passwordHash);
        $this->assertSame($value, $passwordHash->toString());
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

        PasswordHash::fromString($value);
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
    public function it_needs_rehash(): void
    {
        $oldPasswordHash = PasswordHash::fromString('old_hash');
        $freshPasswordHash = PasswordHash::fromString('fresh_hash');

        $hashService = $this->prophesize(PasswordHashService::class);
        $hashService->needsRehash($oldPasswordHash)->willReturn(true);
        $hashService->needsRehash($freshPasswordHash)->willReturn(false);

        $this->assertTrue($oldPasswordHash->needsRehash($hashService->reveal()));
        $this->assertFalse($freshPasswordHash->needsRehash($hashService->reveal()));
    }
}
