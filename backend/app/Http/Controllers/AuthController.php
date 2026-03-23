<?php

namespace App\Http\Controllers;

use App\Services\GoogleService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    public function redirect(GoogleService $googleService): JsonResponse
    {
        return response()->json([
            'url' => $googleService->getAuthUrl(),
        ]);
    }

    public function callback(Request $request, GoogleService $googleService, UserService $userService): JsonResponse
    {
        if ($request->filled('error')) {
            return response()->json([
                'message' => __('Falha ao autorizar no Google.'),
                'error' => $request->string('error')->toString(),
                'error_description' => $request->string('error_description')->toString(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $code = $request->string('code')->toString();
        if ($code === '') {
            return response()->json([
                'message' => __('Erro ao obter o código do Google na URL de callback.'),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $dto = $googleService->getUserByCode($code);

        $user = $userService->handleGoogleLogin($dto);

        return response()->json([
            'user' => [
                'id' => $user->id,
            ],
        ]);
    }
}
