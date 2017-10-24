<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\Command\RegisterUser;
use Oqq\EsUserLogin\Domain\User\Handler\RegisterUserHandler;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Domain\User\UserRepository;
use OqqTest\EsUserLogin\AggregateRootMockFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Handler\RegisterUserHandler
 */
final class RegisterUserHandlerTest extends TestCase
{
    private $identityRepository;
    private $userRepository;
    private $hashService;
    private $handler;

    public function setUp(): void
    {
        $this->identityRepository = $this->prophesize(IdentityRepository::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->hashService = $this->prophesize(PasswordHashService::class);

        $this->handler = new RegisterUserHandler(
            $this->identityRepository->reveal(),
            $this->userRepository->reveal(),
            $this->hashService->reveal()
        );
    }

    /**
     * @test
     */
    public function it_stores_register_attempt(): void
    {
        $emailAddress = EmailAddress::fromString('foo@test.de');
        $identityId = IdentityId::fromEmailAddress($emailAddress);
        $userId = UserId::generate();

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'userId' => $userId,
        ]);

        /** @var User $user */
        $user = AggregateRootMockFactory::create(User::class, [
            'userId' => $userId,
        ]);

        $this->identityRepository->load(Argument::type(IdentityId::class))->willReturn($identity);
        $this->userRepository->get($userId)->willReturn($user);
        $this->userRepository->save($user)->shouldBeCalled();

        $command = new RegisterUser([
            'user_id' => UserId::generate()->toString(),
            'email_address' => $emailAddress->toString(),
            'password' => 'secure',
        ]);

        $handler = $this->handler;
        $handler($command);
    }

    /**
     * @test
     */
    public function it_stores_user_and_identity(): void
    {
        $password = 'secret';
        $passwordHash = PasswordHash::fromString('hash');

        $this->identityRepository->load(Argument::type(IdentityId::class))->willReturn(null);
        $this->hashService->hash($password)->willReturn($passwordHash);

        $this->identityRepository->save(Argument::type(Identity::class))->shouldBeCalled();
        $this->userRepository->save(Argument::type(User::class))->shouldBeCalled();

        $command = new RegisterUser([
            'user_id' => UserId::generate()->toString(),
            'email_address' => EmailAddress::fromString('foo@test.de')->toString(),
            'password' => $password,
        ]);

        $handler = $this->handler;
        $handler($command);
    }
}
