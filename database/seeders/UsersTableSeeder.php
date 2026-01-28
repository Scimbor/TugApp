<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mysql')->table('users')->insert([
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('1234'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard User',
                'email' => 'user@example.com',
                'password' => Hash::make('1234'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TUG User',
                'email' => 'tug@example.com',
                'password' => Hash::make('1234'),
                'role' => 'tug',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
