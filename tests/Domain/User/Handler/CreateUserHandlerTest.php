<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\User\Command\CreateUser;
use Oqq\EsUserLogin\Domain\User\Handler\CreateUserHandler;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Domain\User\UserRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Handler\CreateUserHandler
 */
final class CreateUserHandlerTest extends TestCase
{
    private $userRepository;
    private $handler;

    public function setUp(): void
    {
        $this->userRepository = $this->prophesize(UserRepository::class);

        $this->handler = new CreateUserHandler($this->userRepository->reveal());
    }

    /**
     * @test
     */
    public function it_creates_user(): void
    {
        $this->userRepository->save(Argument::type(User::class))->shouldBeCalled();

        $command = new CreateUser([
            'user_id' => UserId::generate()->toString(),
            'email_address' => 'foo@test.de',
        ]);

        $handler = $this->handler;
        $handler($command);
    }
}
