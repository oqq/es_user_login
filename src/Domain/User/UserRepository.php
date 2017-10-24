<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain\User;

use Oqq\EsUserLogin\Domain\User\Exception\UserNotFoundException;

interface UserRepository
{
    /**
     * @throws UserNotFoundException
     */
    public function get(UserId $userId): User;

    public function save(User $user): void;
}
