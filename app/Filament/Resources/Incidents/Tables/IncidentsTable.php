<?php

namespace App\Filament\Resources\Incidents\Tables;

use App\Filament\Resources\Incidents\IncidentResource;
use App\Filament\Resources\Incidents\Modals\Actions\IncidentUpdater;
use App\Filament\Resources\Incidents\Modals\IncidentModalFormData;
use App\Filament\Resources\Incidents\Modals\Schema\IncidentModalFormSchema;
use App\Models\Incident;
use App\Models\IncidentDepositFee;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
            ->recordUrl(null)
            ->recordAction('edit')
            ->recordActions([
                EditAction::make()
                    ->modal()
                    ->url(null)
                    ->label(fn (Incident $record) => in_array($record->status, Incident::CLOSED_MODIFICATION_ROW_STATUSES) ? 'Podgląd' : 'Edycja')
                    ->schema(IncidentModalFormSchema::components(false))
                    ->fillForm(function (HasSchemas $livewire, Model $record, $table): array {
                        if (!$record instanceof Incident) {
                            return $record->attributesToArray();
                        }
                        return array_merge($record->attributesToArray(), IncidentModalFormData::fillForEdit($record));
                    })
                    ->using(function (array $data, HasSchemas $livewire, Model $record, $table): void {
                        if (!$record instanceof Incident) {
                            $record->update($data);
                            return;
                        }
                        IncidentUpdater::updateFromFormData($record, $data);
                    })
                    ->modalWidth(Width::SevenExtraLarge)
                    ->modalHeading(fn (Action $action): string => 'Zgłoszenie #' . ($action->getRecord()?->getKey() ?? ''))
                    ->modalSubmitAction(fn (Action $action): Action|false => static::canSaveFromModal($action) ? $action : false)
                    ->modalCancelActionLabel(fn (Action $action): string => static::cancelLabelForRecord($action->getRecord())),
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

    private static function canSaveFromModal(Action $action): bool
    {
        $record = $action->getRecord();
        if (!$record instanceof Incident) {
            return true;
        }
        $isClosed = in_array($record->status ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES);
        if (!$isClosed) {
            return true;
        }
        $isCompleted = $record->status === Incident::STATUS_COMPLETED;
        $fee = $record->depositFees()->first();
        $isFeeEditable = !$fee || !in_array($fee->status ?? null, IncidentDepositFee::CLOSED_MODIFICATION_ROW_STATUSES);

        return $isCompleted && $isFeeEditable;
    }

    private static function cancelLabelForRecord(?Model $record): string
    {
        if (!$record instanceof Incident) {
            return __('filament-actions::modal.actions.cancel.label');
        }

        return in_array($record->status ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES)
            ? 'Wróć'
            : __('filament-actions::modal.actions.cancel.label');
    }
}
