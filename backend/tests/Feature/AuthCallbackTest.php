<?php

namespace Tests\Feature;

use App\DTOs\GoogleUserDTO;
use App\Models\User;
use App\Services\GoogleService;
use App\Services\UserService;
use Tests\TestCase;

class AuthCallbackTest extends TestCase
{
    public function test_oauth_callback_returns_user_token(): void
    {
        $googleService = \Mockery::mock(GoogleService::class);
        $userService = \Mockery::mock(UserService::class);

        $dto = new GoogleUserDTO(
            googleId: 'google-user-1',
            email: 'user@example.test',
            token: ['access_token' => 'abc123']
        );

        $user = new User();
        $user->api_token = '11111111-1111-1111-1111-111111111111';

        $googleService->shouldReceive('getUserByCode')
            ->once()
            ->with('oauth-code')
            ->andReturn($dto);

        $userService->shouldReceive('handleGoogleLogin')
            ->once()
            ->with($dto)
            ->andReturn($user);

        $this->app->instance(GoogleService::class, $googleService);
        $this->app->instance(UserService::class, $userService);

        $response = $this->getJson('/api/oauth/google/callback?code=oauth-code');

        $response->assertOk()
            ->assertJson([
                'token' => '11111111-1111-1111-1111-111111111111',
            ]);
    }
}

