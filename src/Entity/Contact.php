<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $firstName;

    #[ORM\Column(type: Types::STRING)]
    private string $lastName;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $email;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string|null $phone
     * @param string|null $email
     */
    public function __construct(string $firstName, string $lastName, ?string $phone, ?string $email)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->email = $email;
    }

    /**
     * Return full name, e.g. "Doe John".
     * @return string
     */
    public function getFullName(): string
    {
        return $this->lastName . ' ' . $this->firstName;
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
     * @return string|string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string|string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string|string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}