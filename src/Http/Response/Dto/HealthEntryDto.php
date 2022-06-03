<?php

declare(strict_types=1);

namespace App\Http\Response\Dto;

use App\Http\Response\HealthStatus;

final class HealthEntryDto
{
    public readonly float $duration;
    public readonly HealthStatus $status;
    public readonly array $tags;

    /**
     * @param float $duration
     * @param HealthStatus $status
     * @param array $tags
     */
    public function __construct(float $duration, HealthStatus $status, array $tags)
    {
        $this->duration = $duration;
        $this->status = $status;
        $this->tags = $tags;
    }
}