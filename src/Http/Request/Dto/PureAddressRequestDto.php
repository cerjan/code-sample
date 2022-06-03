<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class PureAddressRequestDto
{
    #[NotBlank]
    public string $street;
    #[NotBlank(allowNull: true)]
    public ?string $number = null;
    #[NotBlank]
    public string $city;
    #[NotBlank]
    public string $zipCode;
    #[NotBlank]
    public string $country;

    public function setStreet(string $street): void
    {
        $street = preg_replace('/([a-z]+)([0-9]+)/i', '$1 $2', $street);
        $street = preg_replace('/([0-9]+)([a-z]+)/i', '$1 $2', $street);

        $this->street = $street;
    }
}