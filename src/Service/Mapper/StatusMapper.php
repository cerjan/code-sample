<?php

declare(strict_types=1);

namespace App\Service\Mapper;

abstract class StatusMapper implements IStatusMapper
{
    protected array $statuses = [];

    protected function mapStatus(mixed $carrierStatusId): ?int
    {
        foreach ($this->statuses as $statusId => $carrierStatuses) {
            if (in_array($carrierStatusId, $carrierStatuses)) {
                return $statusId;
            }
        }

        return null;
    }
}