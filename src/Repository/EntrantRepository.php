<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Entrant;

class EntrantRepository extends BaseRepository
{
    public function getOrCreate(Contact $contact, ?Company $company, Address $address): Entrant
    {
        if (!$entrant = $this->findOneBy([
            'contact' => $contact,
            'company' => $company,
            'address' => $address,
        ])) {
            $entrant = new Entrant($contact, $company, $address);
        }

        return $entrant;
    }
}