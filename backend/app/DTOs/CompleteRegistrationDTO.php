<?php

namespace App\DTOs;

class CompleteRegistrationDTO
{
    public function __construct(
        public string $token,
        public string $name,
        public string $cpf,
        public string $birthDate,
    ) {}
}
