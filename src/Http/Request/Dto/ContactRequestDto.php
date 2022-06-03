<?php

declare(strict_types=1);

namespace App\Http\Request\Dto;

use App\Utils\Normalizer;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactRequestDto
{
    #[NotBlank]
    public string $firstName;
    #[NotBlank]
    public string $lastName;
    #[NotBlank]
    public string $phone;
    #[NotBlank, Email(mode: 'strict')]
    public string $email;

    public function setPhone(string $phone): void
    {
        $this->phone = Normalizer::removeNumberSpaces($phone);
    }
}