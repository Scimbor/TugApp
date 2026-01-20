<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\CreateActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\EditActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLogs;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogsForm;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogsInfolist;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use App\Models\ActivityLog;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActivityLogsResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getLabel(): ?string
    {
        return 'Log';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Logi';
    }

    protected static ?string $recordTitleAttribute = 'yes';

    public static function form(Schema $schema): Schema
    {
        return ActivityLogsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityLogsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
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
            'index' => ListActivityLogs::route('/'),
            // 'create' => CreateActivityLogs::route('/create'),
            // 'view' => ViewActivityLogs::route('/{record}'),
            // 'edit' => EditActivityLogs::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === User::ADMIN_ROLE;
    }
}
