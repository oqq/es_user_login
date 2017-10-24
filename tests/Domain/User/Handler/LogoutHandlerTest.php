<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\User\Command\Logout;
use Oqq\EsUserLogin\Domain\User\Handler\LogoutHandler;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Domain\User\UserRepository;
use OqqTest\EsUserLogin\AggregateRootMockFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Handler\LogoutHandler
 */
final class LogoutHandlerTest extends TestCase
{
    private $userRepository;
    private $handler;

    public function setUp(): void
    {
        $this->userRepository = $this->prophesize(UserRepository::class);

        $this->handler = new LogoutHandler($this->userRepository->reveal());
    }

    /**
     * @test
     */
    public function it_logs_out(): void
    {
        $userId = UserId::generate();

        /** @var User $user */
        $user = AggregateRootMockFactory::create(User::class, [
            'userId' => $userId,
        ]);

        $this->userRepository->get(Argument::type(UserId::class))->willReturn($user);
        $this->userRepository->save($user)->shouldBeCalled();

        $command = new Logout([
            'user_id' => $userId->toString(),
        ]);

        $handler = $this->handler;
        $handler($command);
    }
}
