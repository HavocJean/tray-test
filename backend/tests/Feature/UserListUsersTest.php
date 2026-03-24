<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserListUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_users_with_filters_and_pagination(): void
    {
        User::query()->create([
            'name' => 'Jean Alpha',
            'cpf' => '11111111111',
            'birth_date' => '1990-01-01',
            'email' => 'alpha@example.test',
            'google_id' => 'g-1',
            'google_token' => ['access_token' => 'a'],
            'api_token' => (string) Str::uuid(),
        ]);

        User::query()->create([
            'name' => 'Maria Beta',
            'cpf' => '22222222222',
            'birth_date' => '1992-01-01',
            'email' => 'beta@example.test',
            'google_id' => 'g-2',
            'google_token' => ['access_token' => 'b'],
            'api_token' => (string) Str::uuid(),
        ]);

        User::query()->create([
            'name' => 'Jean Gamma',
            'cpf' => '33333333333',
            'birth_date' => '1994-01-01',
            'email' => 'gamma@example.test',
            'google_id' => 'g-3',
            'google_token' => ['access_token' => 'c'],
            'api_token' => (string) Str::uuid(),
        ]);

        $response = $this->getJson('/api/users?name=Jean&per_page=1');

        $response->assertOk()
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonCount(1, 'data');

        $firstCpf = $response->json('data.0.cpf');
        $responseCpf = $this->getJson('/api/users?cpf='.$firstCpf);

        $responseCpf->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.cpf', $firstCpf);
    }
}

