<?php

namespace App\DTOs;

class GoogleUserDTO
{
    public function __construct(
        public string $googleId,
        public ?string $email,
        public array $token
    ) {}
}