<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Carrier;
use App\Http\Error\NotFoundException;

class CarrierRepository extends BaseRepository
{
    public function getByCode(string $code): Carrier
    {
        if (!$carrier = $this->findOneByCode($code)) {
            throw new NotFoundException("Carrier `{$code}` not found.");
        };

        return $carrier;
    }
}