<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Manager;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\Command\CreateIdentityForNewUser;
use Oqq\EsUserLogin\Domain\User\Command\CreateUser;
use Oqq\EsUserLogin\Domain\User\Command\RegisterUser;
use Oqq\EsUserLogin\Domain\User\Manager\RegisterUserManager;
use Oqq\EsUserLogin\Domain\User\UserId;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Argument;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Manager\RegisterUserManager
 */
class RegisterUserManagerTest extends TestCase
{
    private $commandBus;
    private $manager;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);

        $this->manager = new RegisterUserManager($this->commandBus->reveal());
    }

    /**
     * @test
     */
    public function it_registers_user(): void
    {
        $this->commandBus->dispatch(Argument::type(CreateUser::class))->shouldBeCalled();
        $this->commandBus->dispatch(Argument::type(CreateIdentityForNewUser::class))->shouldBeCalled();

        $command = new RegisterUser([
           'user_id' => UserId::generate()->toString(),
           'email_address' => 'foo@bar.com',
           'password' => 'secret',
        ]);

        $manager = $this->manager;
        $manager($command);
    }
}
