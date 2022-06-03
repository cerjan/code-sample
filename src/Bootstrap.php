<?php

declare(strict_types=1);

namespace App;

use DI\Container;
use DI\ContainerBuilder;

class Bootstrap
{
    public static function boot(): Container
    {
        $builder = new ContainerBuilder();

        $builder->useAnnotations(true);

        $builder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
        $builder->addDefinitions(require __DIR__ . '/../config/configs.php');

        return $builder->build();
    }
}