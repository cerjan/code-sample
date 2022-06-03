<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use App\Utils\Normalizer;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressRequestDto
{
    #[NotBlank(allowNull: true)]
    public ?string $name = null;
    #[NotBlank]
    public string $street;
    #[NotBlank]
    public string $number;
    #[NotBlank]
    public string $city;
    #[NotBlank]
    public string $zipCode;
    #[NotBlank]
    public string $country;

    public function setNumber(string $number): void
    {
        $this->number = Normalizer::removeNumberSpaces($number);
    }

    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = Normalizer::removeNumberSpaces($zipCode);
    }
}