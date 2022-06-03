<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;

class CompanyRepository extends BaseRepository
{
    public function getOrCreate(array $data): Company
    {
        if (!$company = $this->findOneBy($data)) {
            $company = new Company(...$data);
        }

        return $company;
    }
}