<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Command;

use Assert\Assertion;
use Oqq\EsUserLogin\Domain\Command;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Password;

final class ChangePassword extends Command
{
    /** @var string */
    private $identityId;

    /** @var string */
    private $currentPassword;

    /** @var string */
    private $newPassword;

    public function identityId(): IdentityId
    {
        return IdentityId::fromString($this->identityId);
    }

    public function currentPassword(): Password
    {
        return Password::fromString($this->currentPassword);
    }

    public function newPassword(): Password
    {
        return Password::fromString($this->newPassword);
    }

    public function payload(): array
    {
        return [
            'identity_id' => $this->identityId,
            'current_password' => $this->currentPassword,
            'new_password' => $this->newPassword,
        ];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::choicesNotEmpty($payload, ['identity_id', 'current_password', 'new_password']);

        $this->identityId = $payload['identity_id'];
        $this->currentPassword = $payload['current_password'];
        $this->newPassword = $payload['new_password'];
    }
}
