<?php

declare(strict_types=1);

namespace App\Action\Shipment;

use App\Entity\Shipment;
use App\Http\Response\JsonResponse;
use App\Repository\ShipmentRepository;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

final class UpdateStatusAsync
{
    public function __invoke(ServerRequest $request, ShipmentRepository $repository, AMQPChannel $channel): Response
    {
        /** @var Shipment $shipment */
        foreach ($repository->inTransport() as $shipmentId) {
            $message = new AMQPMessage(json_encode([
                'shipmentId' => (int) $shipmentId,
            ]), [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]);

            $channel->basic_publish($message, 'amq.direct', 'updateStatus');
        }

        return new JsonResponse(null, 204);
    }
}