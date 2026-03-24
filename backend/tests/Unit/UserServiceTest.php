<?php

namespace Tests\Unit;

use App\DTOs\GoogleUserDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    #[Test]
    public function handle_google_login_generates_uuid_and_calls_repository(): void
    {
        $dto = new GoogleUserDTO(
            googleId: 'google-1',
            email: 'user@example.test',
            token: ['access_token' => 'token']
        );

        $expectedUser = new User();
        $expectedUser->api_token = (string) Str::uuid();

        $repository = \Mockery::mock(UserRepository::class);
        $repository->shouldReceive('createOrUpdateGoogleUser')
            ->once()
            ->with(
                $dto,
                \Mockery::on(fn (string $uuid): bool => Str::isUuid($uuid))
            )
            ->andReturn($expectedUser);

        $service = new UserService($repository);

        $result = $service->handleGoogleLogin($dto);

        $this->assertSame($expectedUser, $result);
    }
}

