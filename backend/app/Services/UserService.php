<?php

namespace App\Services;

use App\DTOs\CompleteRegistrationDTO;
use App\DTOs\GoogleUserDTO;
use App\DTOs\UserFilterDTO;
use App\Jobs\SendRegistrationCompletedEmail;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function handleGoogleLogin(GoogleUserDTO $dto): User
    {
        return $this->userRepository->createOrUpdateGoogleUser(
            $dto,
            (string) Str::uuid()
        );
    }

    public function completeRegistration(CompleteRegistrationDTO $dto): User
    {
        $user = $this->userRepository->findByApiToken($dto->token);

        if ($user === null) {
            throw new ModelNotFoundException('Usuário não encontrado para o token informado.');
        }

        $user = $this->userRepository->completeRegistration($user, $dto);

        SendRegistrationCompletedEmail::dispatch($user->id);

        return $user;
    }

    public function listUsers(UserFilterDTO $filters): LengthAwarePaginator
    {
        return $this->userRepository->listWithFilters($filters);
    }
}