<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\IsTrue;

class PackageRequestDto
{
    #[GreaterThan(0)]
    public ?float $weight = null;
    #[GreaterThan(0)]
    public ?float $volume = null;
    #[GreaterThan(0)]
    public ?float $width = null;
    #[GreaterThan(0)]
    public ?float $height = null;
    #[GreaterThan(0)]
    public ?float $length = null;

    #[IsTrue(message: 'Fill all dimension info.')]
    public function isDimensionComplete(): bool
    {
        $attrs = [
            $this->width,
            $this->height,
            $this->length,
        ];

        return match (count(array_filter($attrs, function ($v) { return is_null($v); }))) {
            0, count($attrs) => true,
            default => false
        };
    }
}