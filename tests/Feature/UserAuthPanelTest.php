<?php

use App\Models\User;
use Filament\Pages\Auth\Login;
use Livewire\Livewire;

beforeEach(function () {
    $this->tempAdminUser = User::factory()->create([
        'role' => 'admin',
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('secret123'),
        'is_active' => true,
    ]);

    $this->tempNormalNonActiveUser = User::factory()->create([
        'role' => 'user',
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('secret123'),
        'is_active' => false,
    ]);

    $this->tempNormalActiveUser = User::factory()->create([
        'role' => 'user',
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('secret123'),
        'is_active' => true,
    ]);
});

afterEach(function () {
    $this->tempAdminUser->delete();
    $this->tempNormalNonActiveUser->delete();
    $this->tempNormalActiveUser->delete();
});

it('Logs in user to filament as admin', function () {
    $this->actingAs($this->tempAdminUser);

    $this->assertAuthenticatedAs($this->tempAdminUser);
    $this->get('/panel/activity-logs')->assertOk(); // 200
});

it('Logs in user to filament as no active user', function () {
    $this->actingAs($this->tempNormalNonActiveUser);
    $this->get('/panel')->assertRedirect('/panel/login');
});

it('Logs in user to filament as active user', function () {
    $this->actingAs($this->tempNormalActiveUser);

    $this->assertAuthenticatedAs($this->tempNormalActiveUser);
    $this->get('/panel/activity-logs')->assertStatus(403);
    $this->get('/panel/incidents')->assertStatus(200);
});