<?php

namespace App\Filament\Resources\TugUsers;

use App\Filament\Resources\TugUsers\Pages\CreateTugUsers;
use App\Filament\Resources\TugUsers\Pages\EditTugUsers;
use App\Filament\Resources\TugUsers\Pages\ListTugUsers;
use App\Filament\Resources\TugUsers\Schemas\TugUsersForm;
use App\Filament\Resources\TugUsers\Tables\TugUsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TugUsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function getLabel(): ?string
    {
        return 'Holownik';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Holowniki';
    }

    protected static ?string $recordTitleAttribute = 'TugUser';

    public static function form(Schema $schema): Schema
    {
        return TugUsersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TugUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTugUsers::route('/'),
            'create' => CreateTugUsers::route('/create'),
            'edit' => EditTugUsers::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === User::ADMIN_ROLE;
    }

    public static function canEdit($record): bool
    {
        if ($record instanceof User && !$record->is_active) {
            return false;
        }
        
        return parent::canEdit($record) ?? true;
    }
}
