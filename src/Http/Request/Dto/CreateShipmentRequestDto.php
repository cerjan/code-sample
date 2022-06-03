<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

class CreateShipmentRequestDto
{
    #[Valid, NotNull]
    public ShipmentRequestDto $shipment;
    #[Valid]
    public ?ServicesRequestDto $services = null;
    #[Valid, NotNull]
    public EntrantRequestDto $recipient;
    #[Valid, NotNull]
    public EntrantRequestDto $sender;
    #[Valid, NotBlank] /** @var PackageRequestDto[] */
    public array $packages;
}