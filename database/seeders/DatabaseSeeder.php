<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::updateOrCreate(
            ['email' => 'admin@bhumi.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@bhumi.com',
                'password' => bcrypt('password'),
            ]
        );
    }
}
