<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Country;
use App\Http\Error\NotFoundException;

class CountryRepository extends BaseRepository
{
    public function getByCode(string $code): Country
    {
        if (!$country = $this->findOneByCode($code)) {
            throw new NotFoundException("Country `{$code}` not found.");
        };

        return $country;
    }
}