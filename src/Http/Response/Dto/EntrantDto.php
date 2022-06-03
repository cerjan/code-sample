<?php

declare(strict_types=1);

namespace App\Http\Response\Dto;

class EntrantDto
{
    use DtoFactory;

    public AddressDto $address;
    public CompanyDto $company;
}