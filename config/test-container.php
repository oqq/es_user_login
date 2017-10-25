<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

return (function (): ContainerInterface {

    $aggregator = new ConfigAggregator([
        new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
    ]);

    $config = $aggregator->getMergedConfig();

    $dependencies = $config['dependencies'];
    $dependencies['services']['config'] = $config;

    return new ServiceManager($dependencies);
})();
