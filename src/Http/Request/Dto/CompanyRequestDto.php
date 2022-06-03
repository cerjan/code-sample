<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use App\Utils\Normalizer;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanyRequestDto
{
    #[NotBlank]
    public string $name;
    #[NotBlank]
    public string $idNumber;
    #[NotBlank(allowNull: true)]
    public ?string $idVatNumber = null;

    public function setIdNumber(string $idNumber): void
    {
        $this->idNumber = Normalizer::removeNumberSpaces($idNumber);
    }

    public function setIdVatNumber(?string $idVatNumber): void
    {
        $this->idVatNumber = Normalizer::removeNumberSpaces($idVatNumber);
    }
}