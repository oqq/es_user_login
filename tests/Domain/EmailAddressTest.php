<?php

declare(strict_types = 1);

namespace OqqTest\EsUserLogin\Domain;

use Oqq\EsUserLogin\Domain\EmailAddress;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\EmailAddress
 */
final class EmailAddressTest extends TestCase
{
    /**
     * @test
     * @dataProvider getValidContents
     */
    public function it_creates_with_valid_value(string $value): void
    {
        $emailAddress = EmailAddress::fromString($value);

        $this->assertInstanceOf(EmailAddress::class, $emailAddress);
        $this->assertSame($value, $emailAddress->toString());
    }

    public function getValidContents(): array
    {
        return [
            ['a@b.c'],
            ['eb@burgeins.de'],
            ['EB@BurgEins.de']
        ];
    }

    /**
     * @test
     * @coversNothing
     * @dataProvider getInvalidContents
     */
    public function it_throws_exception_with_invalid_value($value): void
    {
        $this->expectException(\Throwable::class);

        EmailAddress::fromString($value);
    }

    public function getInvalidContents(): array
    {
        return [
            [''],
            [2],
            [-2],
            [null],
            [1.123],
            [[]],
            ['abc.de'],
        ];
    }

    /**
     * @test
     */
    public function it_returns_normalized_address(): void
    {
        $emailAddress = EmailAddress::fromString('EB@BurgEins.de');
        $emailAddressNormalized = EmailAddress::fromString('eb@burgeins.de');

        $this->assertTrue($emailAddress->sameAs($emailAddressNormalized));
        $this->assertSame('eb@burgeins.de', $emailAddress->normalized());
        $this->assertSame('eb@burgeins.de', $emailAddressNormalized->normalized());
    }
}
