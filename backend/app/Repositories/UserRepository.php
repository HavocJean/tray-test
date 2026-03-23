<?php

namespace App\Repositories;

use App\DTOs\GoogleUserDTO;
use App\Models\User;

class UserRepository
{
    public function findByGoogleId(string $googleId): ?User
    {
        return User::where('google_id', $googleId)->first();
    }

    public function createOrUpdateGoogleUser(GoogleUserDTO $dto): User
    {
        return User::updateOrCreate(
            [
                'google_id' => $dto->googleId,
            ],
            [
                'email' => $dto->email,
                'google_token' => $dto->token,
            ]
        );
    }
}