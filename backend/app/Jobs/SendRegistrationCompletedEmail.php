<?php

namespace App\Jobs;

use App\Mail\RegistrationCompletedMail;
use App\Models\User;
use App\Services\GoogleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendRegistrationCompletedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $userId) {}

    public function handle(GoogleService $googleService): void
    {
        $user = User::query()->find($this->userId);

        if ($user === null) {
            return;
        }

        $token = $user->google_token;
        if (! is_array($token)) {
            $token = [];
        }

        $email = $googleService->getEmailFromStoredToken($token);

        if ($email === null || $email === '') {
            $email = $user->email;
        }

        if ($email === null || $email === '') {
            Log::warning('SendRegistrationCompletedEmail: e-mail não encontrado.', [
                'user_id' => $this->userId,
            ]);

            return;
        }

        Mail::to($email)->send(new RegistrationCompletedMail($user));
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('SendRegistrationCompletedEmail: falhou.', [
            'user_id' => $this->userId,
            'message' => $exception?->getMessage(),
        ]);
    }
}
