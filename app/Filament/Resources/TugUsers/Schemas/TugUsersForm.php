<?php

namespace App\Filament\Resources\TugUsers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use App\Models\User;

class TugUsersForm
{
    public static function configure(Schema $schema): Schema
    {   
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password(),
                TextInput::make('api_token')
                    ->label('Token API')
                    ->password()
                    ->revealable()
                    ->disabled()
                    ->dehydrated(false)
                    ->default(function ($record) {
                        if ($record && $record->exists) {
                            $token = \Laravel\Sanctum\PersonalAccessToken::where('user_id', $record->id)
                                ->whereNotNull('plain_token')
                                ->first();
                            return $token ? $token->plain_token : null;
                        }
                        return null;
                    })
                    ->helperText(function ($record) {
                        if ($record && $record->exists) {
                            $token = \Laravel\Sanctum\PersonalAccessToken::where('user_id', $record->id)
                                ->whereNotNull('plain_token')
                                ->first();
                            if ($token) {
                                return 'Token jest zapisany w bazie. Kliknij ikonę oka, aby go odsłonić. Możesz go skopiować lub wygenerować nowy.';
                            }
                        }
                        return 'Wygeneruj token API, aby użytkownik mógł korzystać z aplikacji mobilnej.';
                    })
                    ->visible(fn ($record) => $record && $record->exists),
            ]);
    }
}
