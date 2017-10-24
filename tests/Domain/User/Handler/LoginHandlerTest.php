<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\User\Handler;

use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\Command\Login;
use Oqq\EsUserLogin\Domain\User\Handler\LoginHandler;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Domain\User\UserRepository;
use OqqTest\EsUserLogin\AggregateRootMockFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @covers \Oqq\EsUserLogin\Domain\User\Handler\LoginHandler
 */
final class LoginHandlerTest extends TestCase
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

        $this->handler = new LoginHandler(
            $this->identityRepository->reveal(),
            $this->userRepository->reveal(),
            $this->hashService->reveal()
        );
    }

    /**
     * @test
     */
    public function it_does_nothing_with_unknown_identity(): void
    {
        $this->identityRepository->load(Argument::type(IdentityId::class))->willReturn(null);

        $this->userRepository->save(Argument::type(UserId::class))->shouldNotBeCalled();
        $this->userRepository->save(Argument::type(Identity::class))->shouldNotBeCalled();

        $command = new Login([
            'email_address' => 'foo@test.de',
            'password' => 'secure',
        ]);

        $handler = $this->handler;
        $handler($command);
    }

    /**
     * @test
     */
    public function it_stores_login_with_valid_password(): void
    {
        $emailAddress = EmailAddress::fromString('foo@bar.com');
        $identityId = IdentityId::fromEmailAddress($emailAddress);
        $userId = UserId::generate();
        $passwordHash = PasswordHash::fromString('hash');

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'userId' => $userId,
            'passwordHash' => $passwordHash,
        ]);

        /** @var User $user */
        $user = AggregateRootMockFactory::create(User::class, [
            'userId' => $userId,
        ]);

        $this->identityRepository->load(Argument::type(IdentityId::class))->willReturn($identity);
        $this->identityRepository->save($identity)->shouldBeCalled();

        $this->hashService->isValid('secure', $passwordHash)->willReturn(true);
        $this->hashService->needsRehash($passwordHash)->willReturn(false);

        $this->userRepository->get($userId)->willReturn($user);
        $this->userRepository->save($user)->shouldBeCalled();

        $command = new Login([
            'email_address' => $emailAddress->toString(),
            'password' => 'secure',
        ]);

        $handler = $this->handler;
        $handler($command);
    }

    /**
     * @test
     */
    public function it_stores_login_attempt_with_invalid_password(): void
    {
        $emailAddress = EmailAddress::fromString('foo@bar.com');
        $identityId = IdentityId::fromEmailAddress($emailAddress);
        $userId = UserId::generate();
        $passwordHash = PasswordHash::fromString('hash');

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'userId' => $userId,
            'passwordHash' => $passwordHash,
        ]);

        /** @var User $user */
        $user = AggregateRootMockFactory::create(User::class, [
            'userId' => $userId,
        ]);

        $this->identityRepository->load(Argument::type(IdentityId::class))->willReturn($identity);
        $this->identityRepository->save($identity)->shouldBeCalled();

        $this->hashService->isValid('wrong_password', $passwordHash)->willReturn(false);

        $this->userRepository->get($userId)->willReturn($user);
        $this->userRepository->save($user)->shouldBeCalled();

        $command = new Login([
            'email_address' => $emailAddress->toString(),
            'password' => 'wrong_password',
        ]);

        $handler = $this->handler;
        $handler($command);
    }
}
