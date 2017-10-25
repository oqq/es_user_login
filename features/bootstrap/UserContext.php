<?php

declare(strict_types=1);

namespace OqqFeature\EsUserLogin;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Oqq\EsUserLogin\Domain\User\Command\CreateUser;
use Oqq\EsUserLogin\Domain\User\Command\RegisterUser;
use Oqq\EsUserLogin\Domain\User\User;
use Oqq\EsUserLogin\Domain\User\UserId;

final class UserContext implements Context
{
    /** @var CommandBusContext|null */
    private $commandBusContext;

    /** @var EventStoreContext|null */
    private $eventStoreContext;

    /**
     * @BeforeScenario
     */
    public function contextGatherer(BeforeScenarioScope $scope): void
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        if ($environment->hasContextClass(CommandBusContext::class)) {
            $this->commandBusContext = $environment->getContext(CommandBusContext::class);
        }

        if ($environment->hasContextClass(EventStoreContext::class)) {
            $this->eventStoreContext = $environment->getContext(EventStoreContext::class);
        }
    }

    /**
     * @Given creating an user would fail
     */
    public function creatingAnUserWillFail(): void
    {
        $this->commandBusContext->commandWillFail(CreateUser::class);
    }

    /**
     * @Given i register a new user
     */
    public function registerUser(): void
    {
        try {
            $this->commandBusContext->dispatch(new RegisterUser([
                'user_id' => UserId::generate()->toString(),
                'email_address' => 'foo@bar.de',
                'password' => 'secret',
            ]));
        } catch (\Throwable $exception) {
        }
    }

    /**
     * @Then an user should be created
     */
    public function assertUserWasCreated(): void
    {
        if (!$this->eventStoreContext->aggregateStreamWasCreated(User::class)) {
            throw new \RuntimeException('Identity was not created');
        }
    }
}
