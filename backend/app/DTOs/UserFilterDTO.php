<?php

namespace App\DTOs;

class UserFilterDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $cpf = null,
        public int $perPage = 20,
    ) {}
}
