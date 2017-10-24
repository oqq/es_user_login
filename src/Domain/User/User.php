<?php

declare(strict_types = 1);

namespace Oqq\EsUserLogin\Domain\User;

use Oqq\EsUserLogin\Domain\AggregateRoot;
use Oqq\EsUserLogin\Domain\EmailAddress;
use Prooph\EventSourcing\AggregateChanged;

final class User extends AggregateRoot
{
    /** @var UserId */
    private $userId;

    public static function register(UserId $userId, EmailAddress $emailAddress): self
    {
        $user = new self();
        $user->recordThat(Event\UserWasRegistered::withEmailAddress($userId, $emailAddress));

        return $user;
    }

    public function registerAgain(EmailAddress $emailAddress): void
    {
        $this->recordThat(Event\UserWasRegisteredAgain::withEmailAddress($this->userId, $emailAddress));
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    protected function aggregateId(): string
    {
        return $this->userId->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (true) {
            case $event instanceof Event\UserWasRegistered:
                $this->userId = $event->userId();
                break;
        }
    }
}
