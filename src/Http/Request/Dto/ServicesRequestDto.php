<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use Symfony\Component\Validator\Constraints\Valid;

class ServicesRequestDto
{
    #[Valid]
    public ?ServiceCodRequestDto $cod = null;

    #[Valid]
    public ?ServiceEpodRequestDto $epod = null;
}