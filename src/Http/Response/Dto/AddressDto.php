<?php

declare(strict_types=1);

namespace App\Http\Response\Dto;

class AddressDto
{
    use DtoFactory;

    public string $street;
    public string $number;
    public string $city;
    public string $zipCode;
}