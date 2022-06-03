<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

class EntrantRequestDto
{
    #[Valid]
    public ?CompanyRequestDto $company = null;
    #[Valid, NotNull]
    public ContactRequestDto $contact;
    #[Valid, NotNull]
    public AddressRequestDto $address;
}