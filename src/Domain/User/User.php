<?php

declare(strict_types = 1);

namespace Oqq\EsUserLogin\Domain\User;

use Oqq\EsUserLogin\Domain\AggregateRoot;
use Oqq\EsUserLogin\Domain\EmailAddress;
use Oqq\EsUserLogin\Domain\Identity\Identity;
use Oqq\EsUserLogin\Domain\Password;
use Oqq\EsUserLogin\Domain\PasswordHashService;
use Oqq\EsUserLogin\Domain\User\Exception\IdentityDoesNotMatchUserException;
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

    public function loginWithIdentity(Identity $identity, Password $password, PasswordHashService $hashService): void
    {
        $this->assertIdentityMatchesUser($identity);

        if (! $identity->passwordIsValid($password, $hashService)) {
            $this->recordThat(Event\UserLoginWasDenied::withIdentity($this->userId, $identity->identityId()));

            return;
        }

        $this->recordThat(Event\UserLoggedIn::withIdentity($this->userId, $identity->identityId()));

        if ($identity->passwordNeedsRehash($hashService)) {
            $identity->rehashPassword($password, $hashService);
        }
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

    private function assertIdentityMatchesUser(Identity $identity): void
    {
        if (! $this->userId->sameValueAs($identity->userId())) {
            throw IdentityDoesNotMatchUserException::withIdentityAndUserId(
                $identity->identityId(),
                $this->userId
            );
        }
    }
}
