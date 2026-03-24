<?php

namespace App\Services;

use App\DTOs\GoogleUserDTO;
use Google\Client;
use Google\Service\Oauth2;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

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
        $client->setAccessType('offline');

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

    public function getEmailFromStoredToken(?array $token): ?string
    {
        if ($token === null || $token === []) {
            return null;
        }

        try {
            $client = $this->getClient();
            $client->setAccessToken($token);

            if ($client->isAccessTokenExpired()) {
                $refresh = $client->getRefreshToken();
                if ($refresh !== null) {
                    $client->fetchAccessTokenWithRefreshToken($refresh);
                }
            }

            if ($client->isAccessTokenExpired()) {
                Log::warning('Google OAuth: access token expirado e sem refresh.');

                return null;
            }

            $oauth = new Oauth2($client);
            $googleUser = $oauth->userinfo->get();

            return $googleUser->email ?? null;
        } catch (Throwable $e) {
            Log::warning('Google user info error: ', ['message' => $e->getMessage()]);

            return null;
        }
    }
}