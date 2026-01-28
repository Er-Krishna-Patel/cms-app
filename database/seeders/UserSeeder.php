<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@customcms.local'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'editor@customcms.local'],
            [
                'name' => 'Editor User',
                'password' => bcrypt('password'),
                'role' => 'editor',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'author@customcms.local'],
            [
                'name' => 'Author User',
                'password' => bcrypt('password'),
                'role' => 'author',
                'email_verified_at' => now(),
            ]
        );
    }
}

