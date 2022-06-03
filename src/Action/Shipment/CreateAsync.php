<?php

declare(strict_types=1);

namespace App\Action\Shipment;

use App\Action\RequestDto;
use App\Facade\ShipmentFacade;
use App\Http\Request\Dto\CreateShipmentRequestDto;
use App\Http\Response\JsonResponse;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

final class CreateAsync
{
    #[RequestDto(dto: CreateShipmentRequestDto::class)]
    public function __invoke(ServerRequest $request, ShipmentFacade $shipmentFacade, AMQPChannel $channel): Response
    {
        $shipmentFacade->fromRequest($request->getParsedBody());

        $message = new AMQPMessage(json_encode([
            'shipmentId' => $shipmentFacade->getShipment()->getId(),
        ]), [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);

        $channel->basic_publish($message, 'amq.direct', 'registerShipment');

        return new JsonResponse($shipmentFacade->getResponse(), 201);
    }
}