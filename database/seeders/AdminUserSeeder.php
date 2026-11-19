<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates (or updates) the single dashboard user from ADMIN_* in .env.
 */
final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('portfolio.admin.email');
        $password = config('portfolio.admin.password');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            $this->command?->warn('ADMIN_EMAIL or ADMIN_PASSWORD is empty: the admin user was not created.');

            return;
        }

        $user = User::query()->firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => (string) config('portfolio.admin.name', 'Admin'),
            'password' => $password,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();
    }
}
