<?php

declare(strict_types=1);

return [
    'dependencies' => [
        'aliases' => [
            \Oqq\EsUserLogin\Domain\PasswordHashService::class => \Oqq\EsUserLogin\Infrastructure\NativePasswordHashService::class,
        ],
        'factories' => [
            \Oqq\EsUserLogin\Infrastructure\NativePasswordHashService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'abstract_factories' => [
            \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class,
        ],
    ],

    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Oqq\EsUserLogin\Domain\User\Manager\RegisterUserManager::class => [
            'prooph.command_bus',
        ],

        \Oqq\EsUserLogin\Domain\Identity\Handler\CreateIdentityForNewUserHandler::class => [
            'repository.identity',
            \Oqq\EsUserLogin\Domain\PasswordHashService::class,
        ],

        \Oqq\EsUserLogin\Domain\User\Handler\CreateUserHandler::class => [
            'repository.user',
        ],
    ],
];
