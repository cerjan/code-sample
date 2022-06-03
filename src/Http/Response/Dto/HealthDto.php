<?php

declare(strict_types=1);

namespace App\Http\Response\Dto;

use App\Http\Response\HealthStatus;

final class HealthDto
{
    public readonly HealthStatus $status;
    public readonly float $totalDuration;
    public readonly array|null $entries;

    /**
     * @param HealthStatus $status
     * @param float $totalDuration
     * @param array|null $entries
     */
    public function __construct(HealthStatus $status, float $totalDuration, ?array $entries)
    {
        $this->status = $status;
        $this->totalDuration = $totalDuration;
        $this->entries = $entries;
    }
}