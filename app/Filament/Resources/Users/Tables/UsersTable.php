<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\User;
use App\Filament\Resources\Users\UsersResource;
use Filament\Tables\Filters\SelectFilter;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')->label('Login')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('role')->label('Rola'),
                TextColumn::make('created_at')->label('Data utworzenia'),
                TextColumn::make('updated_at')->label('Data aktualizacji'),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktywny' : 'Dezaktywowany')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('role')
                ->label('Rola')
                ->options(User::ROLES),
            ])
            ->recordUrl(fn (User $record) => $record->is_active 
                ? UsersResource::getUrl('edit', ['record' => $record])
                : null
            )
            ->recordActions([
                EditAction::make()
                    ->visible(fn (User $record) => $record->is_active === true),
                Action::make('deactivate')
                    ->label('Dezaktywuj')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Dezaktywuj użytkownika')
                    ->modalDescription('Czy na pewno chcesz dezaktywować tego użytkownika?')
                    ->modalSubmitActionLabel('Dezaktywuj')
                    ->action(function (User $record) {
                        $record->update(['is_active' => false]);
                        
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Użytkownik dezaktywowany')
                            ->body('Użytkownik został pomyślnie dezaktywowany.')
                            ->send();
                    })
                    ->visible(fn (User $record) => $record->is_active === true),
                Action::make('activate')
                    ->label('Aktywuj')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update(['is_active' => true]);
                    })
                    ->visible(fn (User $record) => $record->is_active === false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
