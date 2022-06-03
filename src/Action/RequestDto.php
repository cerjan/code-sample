<?php

declare(strict_types=1);

namespace App\Action;

use Attribute;

#[Attribute]
class RequestDto
{
    public function __construct(string $dto)
    {
    }
}