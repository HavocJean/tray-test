<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoadTestUserSeeder extends Seeder
{
    private const DEFAULT_COUNT = 150_000;

    private const CHUNK_SIZE = 1000;

    public function run(): void
    {
        $count = (int) (env('LOAD_TEST_USERS', self::DEFAULT_COUNT));

        if ($count < 1) {
            $this->command?->warn('LOAD_TEST_USERS inválido.');
            return;
        }

        $startId = (int) (DB::table('users')->max('id') ?? 0);

        $this->command?->info("Inserindo {$count} usuários em lotes de ".self::CHUNK_SIZE.'...');

        $now = now()->toDateTimeString();
        $inserted = 0;
        $batch = [];

        for ($n = 1; $n <= $count; $n++) {
            $i = $startId + $n;

            $batch[] = [
                'name' => 'Loadtest User '.$i,
                'cpf' => sprintf('%011d', $i),
                'birth_date' => '1990-01-15',
                'email' => 'loadtest_'.$i.'@example.test',
                'email_verified_at' => null,
                'password' => null,
                'remember_token' => null,
                'google_id' => 'loadtest_google_'.$i,
                'google_token' => null,
                'api_token' => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= self::CHUNK_SIZE) {
                DB::table('users')->insert($batch);
                $inserted += count($batch);
                $batch = [];
                $this->command?->info("{$inserted} / {$count}");
            }
        }

        if ($batch !== []) {
            DB::table('users')->insert($batch);
            $inserted += count($batch);
        }

        $this->command?->info("Concluído: {$inserted} registros inseridos.");
    }
}
