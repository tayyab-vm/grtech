<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@grtech.com'],
            [
            'name' => 'Admin',
            'email' => 'admin@grtech.com',
            'password' => 'password',
            'type' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@grtech.com'],
            [
            'name' => 'User',
            'email' => 'user@grtech.com',
            'password' => 'password',
            'type' => 'user',
            ]
        );
    }
}
