<?php

namespace App\Repositories;

use App\DTOs\CompleteRegistrationDTO;
use App\DTOs\GoogleUserDTO;
use App\DTOs\UserFilterDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function findByGoogleId(string $googleId): ?User
    {
        return User::where('google_id', $googleId)->first();
    }

    public function findByApiToken(string $token): ?User
    {
        return User::where('api_token', $token)->first();
    }

    public function createOrUpdateGoogleUser(GoogleUserDTO $dto, string $apiToken): User
    {
        return User::updateOrCreate(
            [
                'google_id' => $dto->googleId,
            ],
            [
                'email' => $dto->email,
                'google_token' => $dto->token,
                'api_token' => $apiToken,
            ]
        );
    }

    public function completeRegistration(User $user, CompleteRegistrationDTO $dto): User
    {
        $user->update([
            'name' => $dto->name,
            'cpf' => $dto->cpf,
            'birth_date' => $dto->birthDate,
        ]);

        return $user->refresh();
    }

    public function listWithFilters(UserFilterDTO $filters): LengthAwarePaginator
    {
        $query = User::select(['api_token', 'name', 'email', 'cpf', 'birth_date', 'created_at']);

        if ($filters->name !== null && $filters->name !== '') {
            $query->where('name', 'LIKE', $filters->name . '%');
        }

        if ($filters->cpf !== null && $filters->cpf !== '') {
            $query->where('cpf', $filters->cpf);
        }

        return $query->orderBy('name')->paginate($filters->perPage);
    }
}