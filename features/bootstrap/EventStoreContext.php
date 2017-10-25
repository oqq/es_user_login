<?php

declare(strict_types=1);

namespace OqqFeature\EsUserLogin;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Prooph\EventStore\EventStore;

final class EventStoreContext implements Context
{
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @BeforeScenario @eventStore
     */
    public function resetEventStore(BeforeScenarioScope $scope): void
    {
        foreach ($this->fetchStreamNames() as $streamName) {
            $this->eventStore->delete($streamName);
        }
    }

    public function aggregateStreamWasCreated(string $aggregateName): bool
    {
        foreach ($this->fetchStreamNames() as $streamName) {
            if (false !== \strpos($streamName->toString(), $aggregateName)) {
                return true;
            }
        }

        return false;
    }

    private function fetchStreamNames(): array
    {
        return $this->eventStore->fetchStreamNames(null, null, 1000);
    }
}
