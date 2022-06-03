<?php

declare(strict_types=1);

namespace App\Service\Carrier;

use App\Entity\AvailableCountry;
use App\Entity\Shipment;
use App\Http\Request\Dto\CreateShipmentRequestDto;
use App\Http\Request\Dto\PureAddressRequestDto;

interface ICarrier
{
    public function registerShipment(Shipment $shipment): void;

    public function cancelShipment(Shipment $shipment): void;

    public function retrieveStatus(Shipment $shipment): void;

    public function possibleDelivery(PureAddressRequestDto $address): ?bool;

    public function isHealthy(): ?bool;
}