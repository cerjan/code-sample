<?php

declare(strict_types=1);

namespace App\Action\Shipment;

use App\Action\RequestDto;
use App\Facade\ShipmentFacade;
use App\Http\Request\Dto\CreateShipmentRequestDto;
use App\Http\Response\JsonResponse;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

final class Create
{
    #[RequestDto(dto: CreateShipmentRequestDto::class)]
    public function __invoke(ServerRequest $request, ShipmentFacade $shipmentFacade): Response
    {
        $shipmentFacade->fromRequest($request->getParsedBody())
            ->register();

        return new JsonResponse($shipmentFacade->getResponse(), 201);
    }
}