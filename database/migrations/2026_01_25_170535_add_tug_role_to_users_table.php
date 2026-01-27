<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'tug' to the role ENUM (admin, user, tug).
     */
    public function up(): void
    {
        DB::connection('mysql')->statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'tug') NOT NULL DEFAULT 'user'"
        );
    }

    /**
     * Reverse the migrations.
     * Reverts role ENUM to admin, user only. Fails if any user has role 'tug'.
     */
    public function down(): void
    {
        DB::connection('mysql')->statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'"
        );
    }
};
