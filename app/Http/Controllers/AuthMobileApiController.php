<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

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

        $token = $user->createToken('mobile-app');
        
        PersonalAccessToken::where([
            ['id', $token->accessToken->id],
        ])->update([
            'user_id' => $user->id,
            'plain_token' => $token->plainTextToken,
        ]);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ], 200);
    }
}
