<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * create a data
     */
    public function run(): void
    {
        // Seed standard test user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password', // will be hashed automatically by casts
            ]
        );

        // Seed admin user
        User::firstOrCreate(
            ['email' => '0ahmedezzat0@gmail.com'],
            [
                'name' => 'Ahmed Ezzat (Admin)',
                'password' => '0000000000', // will be hashed automatically by casts
            ]
        );
    }
}
