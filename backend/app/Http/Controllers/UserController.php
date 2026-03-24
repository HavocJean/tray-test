<?php

namespace App\Http\Controllers;

use App\DTOs\CompleteRegistrationDTO;
use App\DTOs\UserFilterDTO;
use App\Http\Requests\CompleteRegistrationRequest;
use App\Http\Requests\ListUsersRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController
{
    public function __construct(private readonly UserService $userService) {}

    public function complete(CompleteRegistrationRequest $request): JsonResponse
    {
        $dto = new CompleteRegistrationDTO(
            token: $request->validated('token'),
            name: $request->validated('name'),
            cpf: $request->validated('cpf'),
            birthDate: $request->validated('birth_date'),
        );

        $user = $this->userService->completeRegistration($dto);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }

    public function index(ListUsersRequest $request): AnonymousResourceCollection
    {
        $filters = new UserFilterDTO(
            name: $request->validated('name'),
            cpf: $request->validated('cpf'),
            perPage: (int) $request->validated('per_page', 15),
        );

        return UserResource::collection(
            $this->userService->listUsers($filters)
        );
    }
}
