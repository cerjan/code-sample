<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $street;

    #[ORM\Column(type: Types::STRING)]
    private string $number;

    #[ORM\Column(type: Types::STRING)]
    private string $city;

    #[ORM\Column(type: Types::STRING)]
    private string $zipCode;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Country $country;

    /**
     * @param ?string $name
     * @param string $street
     * @param string $number
     * @param string $city
     * @param string $zipCode
     * @param Country $country
     */
    public function __construct(?string $name, string $street, string $number, string $city, string $zipCode, Country $country)
    {
        $this->name = $name;
        $this->street = $street;
        $this->number = $number;
        $this->city = $city;
        $this->zipCode = $zipCode;
        $this->country = $country;
    }

    /**
     * Return street with number.
     * @return string
     */
    public function getAddressLine(): string
    {
        return $this->street . ' ' . $this->number;
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string|string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string|string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string|string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string|string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string|string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @param string|string $zipCode
     */
    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }
}