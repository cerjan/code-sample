<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contact;

class ContactRepository extends BaseRepository
{
    public function getOrCreate(array $data): Contact
    {
        if (!$contact = $this->findOneBy($data)) {
            $contact = new Contact(...$data);
        }

        return $contact;
    }
}