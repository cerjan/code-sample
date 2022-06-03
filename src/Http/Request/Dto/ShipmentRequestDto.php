<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use App\Utils\Carriers\DeliveryTypes;
use App\Utils\Normalizer;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ShipmentRequestDto
{
    #[NotBlank]
    public string $carrier;
    #[NotNull, Choice([DeliveryTypes::DELIVERY, DeliveryTypes::PICKUP])]
    public string $type;
    #[NotBlank]
    public string $deliveryNote;
    #[NotBlank]
    public string $variableSymbol;
    #[NotBlank(allowNull: true)]
    public ?string $reference = null;
    #[NotBlank(allowNull: true)]
    public ?string $shipmentNote = null;

    public function setVariableSymbol(string $variableSymbol): void
    {
        $this->variableSymbol = Normalizer::removeNumberSpaces($variableSymbol);
    }
}