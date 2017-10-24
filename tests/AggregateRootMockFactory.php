<?php

declare(strict_types=1);

namespace OqqTest\EsUserLogin;

use Oqq\EsUserLogin\Domain\AggregateRoot;

final class AggregateRootMockFactory
{
    public static function create(string $aggregateRootClass, array $properties = []): AggregateRoot
    {
        $reflector = new \ReflectionClass($aggregateRootClass);

        /** @var AggregateRoot $aggregateRoot */
        $aggregateRoot = $reflector->newInstanceWithoutConstructor();

        foreach ($properties as $field => $value) {
            $property = $reflector->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($aggregateRoot, $value);
        }

        return $aggregateRoot;
    }
}
