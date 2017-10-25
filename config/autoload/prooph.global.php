<?php

declare(strict_types=1);

return [
    'dependencies' => [
        'aliases' => [
            'prooph.event_store' => \Prooph\EventStore\EventStore::class,
            'prooph.command_bus' => \Prooph\ServiceBus\CommandBus::class,
            \Prooph\EventSourcing\Aggregate\AggregateTranslator::class => \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class,
        ],
        'factories' => [
            \Prooph\EventStore\EventStore::class => \Prooph\EventStore\Container\InMemoryEventStoreFactory::class,
            \Prooph\ServiceBus\EventBus::class => \Prooph\ServiceBus\Container\EventBusFactory::class,
            \Prooph\ServiceBus\CommandBus::class => \Prooph\ServiceBus\Container\CommandBusFactory::class,
            \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Prooph\EventStoreBusBridge\TransactionManager::class => \Prooph\EventStoreBusBridge\Container\TransactionManagerFactory::class,

            'repository.identity' => [\Prooph\EventSourcing\Container\Aggregate\AggregateRepositoryFactory::class, 'identity'],
            'repository.user' => [\Prooph\EventSourcing\Container\Aggregate\AggregateRepositoryFactory::class, 'user'],
        ],
    ],

    'prooph' => [
        'service_bus' => [
            'command_bus' => [
                'plugins' => [
                    \Prooph\EventStoreBusBridge\TransactionManager::class,
                ],
                'router' => [
                    'routes' => [
                        \Oqq\EsUserLogin\Domain\User\Command\RegisterUser::class => \Oqq\EsUserLogin\Domain\User\Manager\RegisterUserManager::class,
                        \Oqq\EsUserLogin\Domain\Identity\Command\CreateIdentityForNewUser::class => \Oqq\EsUserLogin\Domain\Identity\Handler\CreateIdentityForNewUserHandler::class,
                        \Oqq\EsUserLogin\Domain\User\Command\CreateUser::class => \Oqq\EsUserLogin\Domain\User\Handler\CreateUserHandler::class,
                    ],
                ],
            ],
        ],
        'event_sourcing' => [
            'aggregate_repository' => [
                'identity' => [
                    'repository_class' => \Oqq\EsUserLogin\Infrastructure\Repository\EventStoreIdentityRepository::class,
                    'aggregate_type' => \Oqq\EsUserLogin\Domain\Identity\Identity::class,
                    'aggregate_translator' => \Prooph\EventSourcing\Aggregate\AggregateTranslator::class,
                    'one_stream_per_aggregate' => true,
                ],
                'user' => [
                    'repository_class' => \Oqq\EsUserLogin\Infrastructure\Repository\EventStoreUserRepository::class,
                    'aggregate_type' => \Oqq\EsUserLogin\Domain\User\User::class,
                    'aggregate_translator' => \Prooph\EventSourcing\Aggregate\AggregateTranslator::class,
                    'one_stream_per_aggregate' => true,
                ],
            ],
        ],
    ],
];
