<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class ServiceEpodRequestDto
{
    #[NotBlank]
    public string $value;
}