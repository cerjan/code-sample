<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CarrierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity(repositoryClass: CarrierRepository::class)]
class Carrier
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $code;

    #[ORM\OneToMany(mappedBy: 'carrier', targetEntity: AvailableCountry::class)]
    private ArrayCollection|PersistentCollection $availableCountries;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getAvailableCountries(): ArrayCollection|PersistentCollection
    {
        return $this->availableCountries;
    }

    /**
     * @param ArrayCollection|PersistentCollection $availableCountries
     */
    public function setAvailableCountries(ArrayCollection|PersistentCollection $availableCountries): void
    {
        $this->availableCountries = $availableCountries;
    }
}