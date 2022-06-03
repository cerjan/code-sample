<?php

declare(strict_types=1);

use App\Bootstrap;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Prague');

Bootstrap::boot()
    ->get(App::class)
    ->run();