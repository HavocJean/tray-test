<?php

namespace App\Services;

use App\DTOs\GoogleUserDTO;
use Google\Client;
use Google\Service\Oauth2;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Integração OAuth2 / userinfo com Google API PHP Client.
 *
 * Regras de negócio / integração:
 * - Credenciais e redirect vêm de `config/services.php` (env), alinhados ao Google Cloud Console.
 * - Escopos `openid`, `email`, `profile` permitem obter `sub` e e-mail em userinfo.
 * - `access_type=offline` pede refresh token quando o Google conceder (útil para jobs posteriores).
 * - O array de token retornado no login deve ser persistido para chamadas assíncronas (ex.: e-mail pós-cadastro).
 */
class GoogleService
{
    /**
     * Cliente configurado para o fluxo web da aplicação.
     */
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

    /**
     * URL de autorização para o usuário abrir no browser.
     */
    public function getAuthUrl(): string
    {
        return $this->getClient()->createAuthUrl();
    }

    /**
     * Troca o `code` do callback por tokens e monta DTO com identificador Google, e-mail e payload para persistir.
     *
     * @throws RuntimeException se o Google devolver erro no token ou userinfo é inválido.
     */
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

    /**
     * Recupera o e-mail via userinfo usando o token OAuth já salvo (requisito: lib oficial + token persistido).
     *
     * Tenta refresh se o access token estiver expirado; retorna null se impossível ou em falha de rede/API.
     *
     * @param  array<string, mixed>|null  $token  Mesmo formato retornado por `fetchAccessTokenWithAuthCode`
     */
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
            Log::warning('Google userinfo falhou.', ['message' => $e->getMessage()]);

            return null;
        }
    }
}