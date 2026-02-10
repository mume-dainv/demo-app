<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'admin',
            'name' => 'Admin',
            'password' => bcrypt('password'),
            'role' => 'ADMIN',
            'avatar' => 'default.png',
        ]);
    }
}
