<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EntrantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrantRepository::class)]
class Entrant
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Contact::class, cascade: ['persist'])]
    #[ORM\JoinColumn(unique: false, nullable: false)]
    private Contact $contact;

    #[ORM\ManyToOne(targetEntity: Company::class, cascade: ['persist'])]
    #[ORM\JoinColumn(unique: false)]
    private ?Company $company;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(unique: false, nullable: false)]
    private Address $address;

    /**
     * @param Contact $contact
     * @param Company|null $company
     * @param Address $address
     */
    public function __construct(Contact $contact, ?Company $company, Address $address)
    {
        $this->contact = $contact;
        $this->company = $company;
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact(Contact $contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param Company|null $company
     */
    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }
}