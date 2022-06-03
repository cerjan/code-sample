<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Currency;
use App\Http\Error\NotFoundException;

class CurrencyRepository extends BaseRepository
{
    public function getByCode(string $code): Currency
    {
        if (!$currency = $this->findOneByCode($code)) {
            throw new NotFoundException("Currency `{$code}` not found.");
        };

        return $currency;
    }
}