<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin\Domain\Identity\Handler;

use Oqq\EsUserLogin\Domain\Identity\Command\ChangePassword;
use Oqq\EsUserLogin\Domain\Identity\Handler\ChangePasswordHandler;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Identity\IdentityRepository;
use Oqq\EsUserLogin\Domain\PasswordHash;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use OqqTest\EsUserLogin\AggregateRootMockFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @covers \Oqq\EsUserLogin\Domain\Identity\Handler\ChangePasswordHandler
 */
final class ChangePasswordHandlerTest extends TestCase
{
    private $identityRepository;
    private $hashService;
    private $handler;

    public function setUp(): void
    {
        $this->identityRepository = $this->prophesize(IdentityRepository::class);
        $this->hashService = $this->prophesize(PasswordHashService::class);

        $this->handler = new ChangePasswordHandler(
            $this->identityRepository->reveal(),
            $this->hashService->reveal()
        );
    }

    /**
     * @test
     */
    public function it_does_nothing_with_unknown_identity(): void
    {
        $this->identityRepository->load(Argument::type(IdentityId::class))->willReturn(null);
        $this->identityRepository->save(Argument::type(Identity::class))->shouldNotBeCalled();

        $command = new ChangePassword([
            'identity_id' => IdentityId::generate()->toString(),
            'current_password' => 'current_password',
            'new_password' => 'new_password',
        ]);

        $handler = $this->handler;
        $handler($command);
    }

    /**
     * @test
     */
    public function it_stores_changes_on_identity(): void
    {
        $identityId = IdentityId::generate();
        $passwordHash = PasswordHash::fromString('hash');

        $identity = AggregateRootMockFactory::create(Identity::class, [
            'identityId' => $identityId,
            'passwordHash' => $passwordHash,
        ]);

        $this->identityRepository->load(Argument::type(IdentityId::class))->willReturn($identity);
        $this->identityRepository->save($identity)->shouldBeCalled();

        $this->hashService->isValid('current_password', $passwordHash)->willReturn(true);
        $this->hashService->hash('new_password')->willReturn($passwordHash);

        $command = new ChangePassword([
            'identity_id' => IdentityId::generate()->toString(),
            'current_password' => 'current_password',
            'new_password' => 'new_password',
        ]);

        $handler = $this->handler;
        $handler($command);
    }
}
