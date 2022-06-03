<?php

declare(strict_types=1);

namespace App\Service\Mapper;

use App\Entity\ShipmentHistory;
use App\Entity\Status;
use DateTime;

class UPSStatusMapper extends StatusMapper
{
    protected array $statuses = [
        Status::STATUS_DELIVERED => ['D'],
        Status::STATUS_IN_TRANSIT => ['I', 'O'],
        Status::STATUS_RETURNED => ['RS']
    ];

    public static function from(mixed $carrierHistoryRecord): ShipmentHistory
    {
        return new ShipmentHistory(
            (new self)->mapStatus(trim($carrierHistoryRecord->status->type)),
            trim($carrierHistoryRecord->status->type),
            DateTime::createFromFormat('YmdHis', $carrierHistoryRecord->date . $carrierHistoryRecord->time),
            $carrierHistoryRecord->status->description
        );
    }
}