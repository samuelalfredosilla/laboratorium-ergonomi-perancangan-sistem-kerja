<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $username = 'admin';
        $password = Str::password(14);

        // Matched by email (the pre-existing unique identifier) so re-running this
        // seeder updates the same admin row instead of colliding on email/username.
        User::updateOrCreate(
            ['email' => 'admin@epsk.trunojoyo.ac.id'],
            [
                'name' => 'Admin EPSK',
                'username' => $username,
                'password' => $password,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Admin user ready -> username: {$username} | password: {$password}");
    }
}
