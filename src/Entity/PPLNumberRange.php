<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PPLNumberRangeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PPLNumberRangeRepository::class)]
#[ORM\Table(name: 'ppl_number_range')]
#[ORM\UniqueConstraint(fields: ['productCode', 'fromNumber', 'toNumber'])]
class PPLNumberRange
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $productCode;

    #[ORM\Column(type: Types::STRING)]
    private string $productName;

    #[ORM\Column(type: Types::BIGINT)]
    private int $fromNumber;

    #[ORM\Column(type: Types::BIGINT)]
    private int $toNumber;

    #[ORM\Column(type: Types::BIGINT)]
    private int $currentNumber;

    /**
     * @param string $productCode
     * @param string $productName
     * @param int $fromNumber
     * @param int $toNumber
     */
    public function __construct(string $productCode, string $productName, int $fromNumber, int $toNumber)
    {
        $this->productCode = $productCode;
        $this->productName = $productName;
        $this->fromNumber = $fromNumber;
        $this->currentNumber = $fromNumber;
        $this->toNumber = $toNumber;
    }

    /**
     * @return string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return int
     */
    public function getCurrentNumber(): int
    {
        return $this->currentNumber;
    }

    public function next(): int
    {
        return $this->currentNumber++;
    }
}