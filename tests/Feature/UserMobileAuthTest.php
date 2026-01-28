<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->tempUser = User::factory()->create([
        'role' => 'tug',
        'email' => time().'_tempuser@test.com',
        'password' => Hash::make('secret123'),
    ]);
});

afterEach(function () {
    $this->tempUser->delete();
});

it('Logs in user, generates token, and logs out', function () {
    $user = $this->tempUser;

    $loginResponse = $this->post('/api/mobile/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $loginResponse->assertStatus(200);
    $loginResponse->assertJsonStructure(['token', 'user']);

    $token = $loginResponse->json('token');

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => get_class($user),
    ]);

    $logoutResponse = $this
        ->withHeader('Authorization', 'Bearer ' . $token)
        ->post('/api/mobile/logout');

    $logoutResponse->assertStatus(200);
    $logoutResponse->assertJson(['message' => 'Logged out']);

    expect($user->tokens()->count())->toBe(0);
});
