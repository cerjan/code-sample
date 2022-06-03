<?php

declare(strict_types=1);

namespace App\Http\Response;

enum HealthStatus: string
{
    case HEALTHY = 'Healthy';
    case UNAVAILABLE = 'Unavailable';
    case UNKNOWN = 'Unknown';

    public function code(): int
    {
        return match($this) {
            self::HEALTHY => 200,
            self::UNKNOWN => 501,
            self::UNAVAILABLE => 503,
        };
    }
}