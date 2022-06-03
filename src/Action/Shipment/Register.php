<?php

declare(strict_types=1);

namespace App\Action\Shipment;

use App\Facade\ShipmentFacade;
use App\Http\Response\JsonResponse;

final class Register
{
    public function __invoke(ShipmentFacade $facade, string $deliveryNote)
    {
        $facade->fromDeliveryNote($deliveryNote)
            ->register();

        return new JsonResponse(null, 204);
    }
}