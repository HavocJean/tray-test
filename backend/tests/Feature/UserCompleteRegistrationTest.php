<?php

namespace Tests\Feature;

use App\Jobs\SendRegistrationCompletedEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserCompleteRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_complete_registration_with_token(): void
    {
        Queue::fake();

        $user = User::query()->create([
            'email' => 'oauth@example.test',
            'google_id' => 'google-123',
            'google_token' => ['access_token' => 'abc'],
            'api_token' => (string) Str::uuid(),
        ]);

        $payload = [
            'token' => $user->api_token,
            'name' => 'Jean Marques',
            'cpf' => '12345678901',
            'birth_date' => '1991-04-10',
        ];

        $response = $this->putJson('/api/users/complete', $payload);

        $response->assertOk()
            ->assertJsonFragment([
                'uuid' => $user->api_token,
                'name' => 'Jean Marques',
                'cpf' => '12345678901',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jean Marques',
            'cpf' => '12345678901',
        ]);

        Queue::assertPushed(SendRegistrationCompletedEmail::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });
    }
}

