<?php

namespace App\Services;

use App\DTOs\GoogleUserDTO;
use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function handleGoogleLogin(GoogleUserDTO $dto): User
    {
        return $this->userRepository->createOrUpdateGoogleUser($dto);
    }
}