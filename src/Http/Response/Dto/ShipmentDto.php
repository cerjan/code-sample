<?php

declare(strict_types=1);

namespace App\Http\Response\Dto;

class ShipmentDto
{
    use DtoFactory;

    public int $id;
    public string $deliveryNote;
    public string $type;
    public CarrierDto $carrier;
    public EntrantDto $recipient;
}