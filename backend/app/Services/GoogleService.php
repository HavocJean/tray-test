<?php

namespace App\Services;

use App\DTOs\GoogleUserDTO;
use Google\Client;
use Google\Service\Oauth2;
use RuntimeException;

class GoogleService
{
    public function getClient(): Client
    {
        $client = new Client;
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setRedirectUri((string) config('services.google.redirect'));
        $client->setScopes([
            'openid',
            'email',
            'profile',
        ]);

        return $client;
    }

    public function getAuthUrl(): string
    {
        return $this->getClient()->createAuthUrl();
    }

    public function getUserByCode(string $code): GoogleUserDTO
    {
        $client = $this->getClient();

        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new RuntimeException(
                (string) ($token['error_description'] ?? $token['error'])
            );
        }

        $client->setAccessToken($token);

        $oauth = new Oauth2($client);
        $googleUser = $oauth->userinfo->get();

        if ($googleUser->id === null || $googleUser->id === '') {
            throw new RuntimeException('Resposta do Google sem identificador de usuário.');
        }

        return new GoogleUserDTO(
            googleId: (string) $googleUser->id,
            email: $googleUser->email,
            token: $token
        );
    }
}