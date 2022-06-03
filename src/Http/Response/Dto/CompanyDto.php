<?php

declare(strict_types=1);

namespace App\Http\Response\Dto;

class CompanyDto
{
    use DtoFactory;

    public string $name;
    public string $idNumber;
    public ?string $idVatNumber;
}