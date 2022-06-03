<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $idNumber;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $idVatNumber;

    /**
     * @param string $name
     * @param string $idNumber
     * @param string|null $idVatNumber
     */
    public function __construct(string $name, string $idNumber, ?string $idVatNumber)
    {
        $this->name = $name;
        $this->idNumber = $idNumber;
        $this->idVatNumber = $idVatNumber;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|string
     */
    public function getIdNumber(): string
    {
        return $this->idNumber;
    }

    /**
     * @param string|string $idNumber
     */
    public function setIdNumber(string $idNumber): void
    {
        $this->idNumber = $idNumber;
    }

    /**
     * @return string|string|null
     */
    public function getIdVatNumber(): ?string
    {
        return $this->idVatNumber;
    }

    /**
     * @param string|string|null $idVatNumber
     */
    public function setIdVatNumber(?string $idVatNumber): void
    {
        $this->idVatNumber = $idVatNumber;
    }
}