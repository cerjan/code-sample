<?php

declare(strict_types=1);

namespace App\Action\Shipment;

use App\Facade\ShipmentFacade;
use App\Http\Response\JsonResponse;
use Nyholm\Psr7\Response;

final class UpdateStatus
{
    public function __invoke(ShipmentFacade $shipmentFacade, string $deliveryNote): Response
    {
        $shipmentFacade->fromDeliveryNote($deliveryNote)
            ->retrieveStatus();

        return new JsonResponse(null, 204);
    }
}