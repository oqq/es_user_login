<?php

declare(strict_types=1);

namespace OqqFeature\EsUserLogin;

use Behat\Behat\Context\Context;
use Oqq\EsUserLogin\Domain\Command;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\CommandBus;

final class CommandBusContext implements Context
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function commandWillFail(string $command): void
    {
        $this->commandBus->attach(CommandBus::EVENT_DISPATCH, function (ActionEvent $actionEvent) use ($command) {
            $messageName = (string) $actionEvent->getParam(CommandBus::EVENT_PARAM_MESSAGE_NAME);

            if ($command === $messageName) {
                $actionEvent->setParam(CommandBus::EVENT_PARAM_MESSAGE_HANDLER, function () use ($command) {
                    throw new \RuntimeException(sprintf('command "%s" failed!', $command));
                });
            }
        }, CommandBus::PRIORITY_ROUTE - 1);
    }

    public function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
