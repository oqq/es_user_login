<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Handler;

use Oqq\EsUserLogin\Domain\Identity\Command\CreateIdentityForNewUser;
use Oqq\EsUserLogin\Domain\Identity\Handler\CreateIdentityForNewUserHandler;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\UserId;
use OqqTest\EsUserLogin\AggregateRootMockFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Handler\CreateIdentityForNewUserHandler
 */
final class CreateIdentityForNewUserHandlerTest extends TestCase
{
    private $identityRepository;
    private $hashService;
    private $handler;

    public function setUp(): void
    {
        $this->identityRepository = $this->prophesize(IdentityRepository::class);
        $this->hashService = $this->prophesize(PasswordHashService::class);

        $this->handler = new CreateIdentityForNewUserHandler(
            $this->identityRepository->reveal(),
            $this->hashService->reveal()
        );
    }

    /**
     * @test
     */
    public function it_stores_new_identity(): void
    {
        $identityId = IdentityId::generate();
        $password = Password::fromString('secret');
        $passwordHash = PasswordHash::fromString('hash');

        $this->identityRepository->load($identityId)->willReturn(null);
        $this->identityRepository->save(Argument::type(Identity::class))->shouldBeCalled();

        $this->hashService->hash('secret')->willReturn($passwordHash);

        $command = CreateIdentityForNewUser::withPassword(
            $identityId,
            UserId::generate(),
            $password
        );

        $handler = $this->handler;
        $handler($command);
    }

    /**
     * @test
     */
    public function it_stores_reuse_of_identity(): void
    {
        $identityId = IdentityId::generate();
        $password = Password::fromString('secret');

        /** @var Identity $identity */
        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
        ]);

        $this->identityRepository->load($identityId)->willReturn($identity);
        $this->identityRepository->save($identity)->shouldBeCalled();

        $command = CreateIdentityForNewUser::withPassword(
            $identityId,
            UserId::generate(),
            $password
        );

        $handler = $this->handler;
        $handler($command);
    }
}
