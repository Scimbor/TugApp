<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if is_active was changed
        if (!$user->wasChanged('is_active')) {
            return;
        }

        // Check if user is of type 'tug'
        if ($user->role !== User::TUG_ROLE) {
            return;
        }

        $originalIsActive = $user->getOriginal('is_active');

        // If user was deactivated (is_active changed from true to false)
        if (!$user->is_active && $originalIsActive) {
            // Delete all API tokens for this user
            $user->tokens()->delete();
        }

        // If user was activated (is_active changed from false to true)
        if ($user->is_active && !$originalIsActive) {
            // Delete any existing tokens first
            $user->tokens()->delete();

            // Generate a new token automatically
            $token = $user->createToken('mobile-app-token');

            // Store plain text token and user_id in personal_access_tokens table
            DB::connection('mysql')->table('personal_access_tokens')
                ->where('id', $token->accessToken->id)
                ->update([
                    'user_id' => $user->id,
                    'plain_token' => $token->plainTextToken,
                ]);
        }
    }
}
