<?php

declare(strict_types=1);

namespace OqqFeature\EsUserLogin;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Oqq\EsUserLogin\Domain\Identity\Identity;

final class IdentityContext implements Context
{
    /** @var EventStoreContext|null */
    private $eventStoreContext;

    /**
     * @BeforeScenario
     */
    public function contextGatherer(BeforeScenarioScope $scope): void
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        if ($environment->hasContextClass(EventStoreContext::class)) {
            $this->eventStoreContext = $environment->getContext(EventStoreContext::class);
        }
    }

    /**
     * @Then an identity should be created
     */
    public function assertIdentityWasCreated(): void
    {
        if (!$this->eventStoreContext->aggregateStreamWasCreated(Identity::class)) {
            throw new \RuntimeException('Identity was not created');
        }
    }

    /**
     * @Then an identity should not be created
     */
    public function assertIdentityWasNotCreated(): void
    {
        if ($this->eventStoreContext->aggregateStreamWasCreated(Identity::class)) {
            throw new \RuntimeException('Identity was created');
        }
    }
}
