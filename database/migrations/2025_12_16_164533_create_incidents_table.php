<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_panel_id')->constrained('users')->onDelete('cascade');
            $table->string('vehicle_number');
            $table->string('vehicle_vin');
            $table->string('vehicle_brand');
            $table->string('vehicle_model');
            $table->string('vehicle_type');
            $table->string('description');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('incidents');
    }
};