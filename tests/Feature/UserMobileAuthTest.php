<?php

use App\Models\User;

it('Get user token', function () {
    $response = $this->post('/api/mobile/login', [
        'email' => 'holownik@wp.pl',
        'password' => '1234',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'token',
        'user',
    ]);
});

it('Logout user', function () {
    $user = User::where('email', 'holownik@wp.pl')->first();
    $plainToken = $user->personalAccessToken->plain_token;

    $response = $this
        ->withHeader('Authorization', 'Bearer ' . $plainToken)
        ->post('/api/mobile/logout');

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Logged out',
    ]);

    expect($user->tokens()->count())->toBe(0);
});

