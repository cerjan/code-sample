<?php

declare(strict_types=1);

use App\Action;
use Nette\Utils\Random;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;

return function (App $app) {
    $app->group('/api/v{version:1}', function (RouteCollectorProxyInterface $route) {
        $route->group('/shipments', function (RouteCollectorProxyInterface $route) {
            $route->get('', Action\Shipment\Read::class);

            $route->post('', Action\Shipment\Create::class);
            $route->post('/async', Action\Shipment\CreateAsync::class);

            $route->put('/{deliveryNote}/register', Action\Shipment\Register::class);
            $route->put('/{deliveryNote}/status', Action\Shipment\UpdateStatus::class);
            $route->put('/{deliveryNote}/cancel', Action\Shipment\Cancel::class);

            $route->put('/update-status/async', Action\Shipment\UpdateStatusAsync::class);
        });

        $route->group('/addresses', function (RouteCollectorProxyInterface $route) {
            $route->post('/possible-delivery[/{carrierCode}]', Action\Address\PossibleDelivery::class);
            $route->post('/validate', Action\Address\Validate::class);
        });

        $route->group('/carriers', function (RouteCollectorProxyInterface $route) {
            $route->get('/health[/{carrierCode}]', Action\Carrier\IsHealthy::class);
        });
    });
};