<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.admin.email');
        $password = config('app.admin.password');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException('ADMIN_EMAIL dan ADMIN_PASSWORD wajib diatur sebelum menjalankan AdminSeeder.');
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => config('app.admin.name') ?: 'Administrator',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
            ],
        );
    }
}
