<?php

declare(strict_types=1);

namespace App\Service\Mapper;

use App\Entity\ShipmentHistory;

interface IStatusMapper
{
    public static function from(mixed $carrierHistoryRecord): ShipmentHistory;
}