<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\Identity\Command;

use Oqq\EsUserLogin\Domain\Command;
use Oqq\EsUserLogin\Domain\Identity\IdentityId;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\User\UserId;

final class CreateIdentityForNewUser extends Command
{
    /** @var IdentityId */
    private $identityId;

    /** @var UserId */
    private $userId;

    /** @var Password */
    private $password;

    public static function withPassword(IdentityId $identityId, UserId $userId, Password $password): self
    {
        $command = new self([
            'identity_id' => $identityId->toString(),
            'user_id' => $userId->toString(),
        ]);

        $command->identityId = $identityId;
        $command->userId = $userId;
        $command->password = $password;

        return $command;
    }

    public function identityId(): IdentityId
    {
        return $this->identityId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function password(): Password
    {
        return $this->password;
    }
}
