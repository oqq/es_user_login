<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogout\Domain\User\Command;

use Oqq\EsUserLogin\Domain\User\Command\Logout;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Command\Logout
 */
final class LogoutTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_command_from_payload(): void
    {
        $userId = UserId::generate();

        $payload = [
            'user_id' => $userId->toString(),
        ];

        $command = new Logout($payload);

        $this->assertEquals($payload, $command->payload());

        $this->assertInstanceOf(UserId::class, $command->userId());
        $this->assertTrue($command->userId()->sameValueAs($userId));
    }

    /**
     * @test
     * @coversNothing
     */
    public function it_throws_exception_with_invalid_user_id(): void
    {
        $this->expectException(\Throwable::class);

        $payload = [
            'user_id' => 'foo',
        ];

        $command = new Logout($payload);
        $command->userId();
    }

    /**
     * @test
     * @coversNothing
     * @dataProvider invalidPayload
     */
    public function it_throws_exception_with_invalid_payload(array $invalidPayload): void
    {
        $this->expectException(\Throwable::class);

        new Logout($invalidPayload);
    }

    public function invalidPayload(): \Generator
    {
        $validValues = [
            'user_id' => UserId::generate()->toString(),
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
