<?php

declare(strict_types=1);

namespace App\Entity;

use ReflectionClass;

class Status
{
    const STATUS_PENDING = 1;
    const STATUS_REGISTERED = 2;
    const STATUS_UNKNOWN = 3;
    const STATUS_LABELED = 4;
    const STATUS_CANCELLED = 5;
    const STATUS_IN_TRANSIT = 6;
    const STATUS_DELIVERED = 7;
    const STATUS_UNDELIVERED = 8;
    const STATUS_RETURNED = 9;

    const CLOSING_STATUSES = [self::STATUS_DELIVERED, self::STATUS_RETURNED, self::STATUS_CANCELLED];

    private int $statusId;

    /**
     * @param int $status
     */
    public function __construct(int $status)
    {
        $this->statusId = $status;
    }

    public static function toArray(): array
    {
        $reflect = new ReflectionClass(__CLASS__);

        return array_flip($reflect->getConstants());
    }

    /**
     * @return int
     */
    public function getStatusId(): int
    {
        return $this->statusId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId(int $statusId): void
    {
        $this->statusId = $statusId;
    }
}