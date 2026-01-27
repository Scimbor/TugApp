<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthMobileApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Nieprawidłowe dane logowania',
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Wylogowano',
        ]);
    }
}
