<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Filament\Resources\Users\Pages\CreateUsers;
use App\Models\User;

class UsersForm
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
                Select::make('role')
                    ->label('Rola')
                    ->options(User::ROLES)
                    ->required(),
            ]);
    }
}
