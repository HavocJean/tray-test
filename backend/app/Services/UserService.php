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

/**
 * Orquestra o domínio de usuário: OAuth Google, cadastro complementar e listagem.
 */
class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    /**
     * Processa retorno do OAuth: persiste token Google e dados básicos, renova `api_token`.
     *
     * @throws \Throwable propagado pelo repositório/Eloquent em falha de persistência
     */
    public function handleGoogleLogin(GoogleUserDTO $dto): User
    {
        return $this->userRepository->createOrUpdateGoogleUser(
            $dto,
            (string) Str::uuid()
        );
    }

    /**
     * Completar o registro do usuáro com nome, CPF e data de nascimento.
     *
     * @throws ModelNotFoundException quando `api_token` não existe  ou é inválido.
     */
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

    /**
     * Lista usuários com filtros opcionais (nome prefixo, CPF exato) e paginação obrigatória.
     *
     * @return LengthAwarePaginator<User>
     */
    public function listUsers(UserFilterDTO $filters): LengthAwarePaginator
    {
        return $this->userRepository->listWithFilters($filters);
    }
}