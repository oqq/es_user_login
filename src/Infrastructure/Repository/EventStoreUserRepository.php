<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Infrastructure\Repository;

use Oqq\EsUserLogin\Domain\User\Exception\UserNotFoundException;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;
use Oqq\EsUserLogin\Domain\User\UserRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

final class EventStoreUserRepository extends AggregateRepository implements UserRepository
{
    /**
     * @throws UserNotFoundException
     */
    public function get(UserId $userId): User
    {
        /** @var User $user */
        $user = $this->getAggregateRoot($userId->toString());

        if (!$user) {
            throw UserNotFoundException::withUserId($userId);
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->saveAggregateRoot($user);
    }
}
