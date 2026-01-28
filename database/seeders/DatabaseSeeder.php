<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tutaj możesz wywołać inne seedery
        $this->call(UsersTableSeeder::class);
    }
}
