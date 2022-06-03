<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ServiceCodRequestDto
{
    #[NotNull, GreaterThan(0)]
    public float $value;
    #[NotBlank]
    public string $currency;
}