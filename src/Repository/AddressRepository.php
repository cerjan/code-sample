<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Address;

class AddressRepository extends BaseRepository
{
    public function getOrCreate(array $data): Address
    {
        if (!$address = $this->findOneBy($data)) {
            $address = new Address(...$data);
        }

        return $address;
    }
}