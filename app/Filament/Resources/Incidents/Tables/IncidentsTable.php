<?php

namespace App\Filament\Resources\Incidents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Incident;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Forms\Components;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Incidents\IncidentResource;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('vehicle_number')->label('Numer rejestracyjny'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Incident::STATUS_OPTIONS[$state] ?? $state)
                    ->color(fn (string $state): string => Incident::STATUS_COLORS[$state] ?? 'gray'),
                TextColumn::make('created_at')->label('Data utworzenia'),
                TextColumn::make('updated_at')->label('Data aktualizacji'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Incident::STATUS_OPTIONS)
                    ->multiple(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Od'),
                        DatePicker::make('created_until')->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                    Filter::make('vehicle_number')
                        ->form([
                            TextInput::make('vehicle_number')->label('Numer rejestracyjny'),
                        ])
                        ->query(function (Builder $query, array $data) {
                            return $query->where('vehicle_number', 'like', '%' . $data['vehicle_number'] . '%');
                    }),
            ])
            ->recordUrl(fn (Incident $record) => IncidentResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                EditAction::make()
                    ->label(fn (Incident $record) => in_array($record->status, Incident::CLOSED_MODIFICATION_ROW_STATUSES) ? 'Podgląd' : 'Edycja'),
                DeleteAction::make()
                    ->visible(fn (Incident $record) => !in_array($record->status, Incident::CLOSED_MODIFICATION_ROW_STATUSES)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
