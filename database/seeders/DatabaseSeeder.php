<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Demo User',
                'email' => 'user@example.com',
                'role' => UserRole::User,
            ],
            [
                'name' => 'Demo Administrator',
                'email' => 'admin@example.com',
                'role' => UserRole::Admin,
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::query()->firstOrNew(['email' => $account['email']]);
            $user->name = $account['name'];
            $user->password = 'password';
            $user->role = $account['role'];
            $user->email_verified_at = now();
            $user->save();
        }
    }
}
