<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ActivityLog;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')->label('E-mail'),
                TextColumn::make('user.name')->label('Nazwa'),
                TextColumn::make('user.role')->label('Rola'),
                TextColumn::make('event')
                    ->formatStateUsing(fn (string $state): string => ActivityLog::ACTIONS_NAMES[$state] ?? $state)
                    ->label('Zdarzenie'),
                TextColumn::make('subject_type')->label('Obiekt'),
                TextColumn::make('properties')->label('Właściwości'),
                TextColumn::make('created_at')->label('Czas zdarzenia'),
            ])
            ->filters([
                //
            ])
            ->recordActions([

            ])
            ->toolbarActions([

            ])
            ->defaultSort('created_at', 'desc');
    }
}
