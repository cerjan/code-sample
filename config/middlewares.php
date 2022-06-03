<?php

declare(strict_types=1);

use App\Http\Handler\HttpErrorHandler;
use App\Http\Middleware\RequestBodyMiddleware;
use Monolog\Logger;
use Slim\App;

return function (App $app, array $config, Logger $logger) {
    $app->add(RequestBodyMiddleware::class);
    $app->addRoutingMiddleware();
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorHandler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory(), $logger);
    $errorHandler->forceContentType('application/json');
    $errorMiddleware->setDefaultErrorHandler($errorHandler);
};