<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Filament\Resources\Incidents\IncidentResource;
use App\Models\Incident;
use App\Models\IncidentDepositFee;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function defaultForm(Schema $schema): Schema
    {
        return parent::defaultForm($schema);
    }

    protected function getFormActions(): array
    {
        $isClosed = in_array($this->record->status ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES);
        $isCompleted = $this->record->status === Incident::STATUS_COMPLETED;
        $fee = $this->record->depositFees()->first();
        $isFeeEditable = !$fee || !in_array($fee->status ?? null, IncidentDepositFee::CLOSED_MODIFICATION_ROW_STATUSES);

        if ($isClosed) {
            return [
                ...($isCompleted && $isFeeEditable ? [$this->getSaveFormAction()] : []),
                $this->getCancelFormAction()->label('Wróć'),
            ];
        }

        return parent::getFormActions();
    }

    protected function getRedirectUrl(): ?string
    {
        if (in_array($this->record->status ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES)) {
            return IncidentResource::getUrl('edit', ['record' => $this->record]);
        }

        return parent::getRedirectUrl();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_panel_id'] = auth()->id();

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $incident = $this->record;

        if ($incident->address) {
            $data['address'] = [
                'street' => $incident->address->street,
                'house_number' => $incident->address->house_number,
                'apartment_number' => $incident->address->apartment_number,
                'city' => $incident->address->city,
                'zip' => $incident->address->zip,
            ];
        }

        $fee = $incident->depositFees()->first();
        
        if ($fee) {
            $data['depositFee'] = [
                'fee' => $fee->fee,
                'description' => $fee->description,
                'status' => $fee->status,
                'payment_method' => $fee->payment_method,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $isClosed = in_array($this->record->status ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES);

        if ($isClosed) {
            return $data;
        }

        if (isset($data['address'])) {
            $addressData = $data['address'];
            unset($data['address']);

            $incident = $this->record;
            
            if ($incident->address) {
                $incident->address->update($addressData);
            } else {
                $incident->address()->create($addressData);
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (!$record instanceof Incident) {
            return parent::handleRecordUpdate($record, $data);
        }

        if ($record->status === Incident::STATUS_COMPLETED) {
            $formData = $this->form->getRawState();
            $payload = [
                'fee' => (float) (data_get($formData, 'depositFee.fee') ?? $formData['depositFee.fee'] ?? 0),
                'description' => data_get($formData, 'depositFee.description') ?? $formData['depositFee.description'] ?? null,
                'status' => data_get($formData, 'depositFee.status') ?? $formData['depositFee.status'] ?? null,
                'payment_method' => data_get($formData, 'depositFee.payment_method') ?? $formData['depositFee.payment_method'] ?? null,
            ];
            $this->saveDepositFee($record, $payload);

            return $record;
        }

        if (in_array($record->status ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES)) {
            return $record;
        }

        return parent::handleRecordUpdate($record, $data);
    }

    protected function saveDepositFee(Incident $incident, array $payload): void
    {
        $fee = $incident->depositFees()->first();

        if ($fee && in_array($fee->status ?? null, IncidentDepositFee::CLOSED_MODIFICATION_ROW_STATUSES)) {
            return;
        }

        $data = [
            'fee' => (float) ($payload['fee'] ?? 0),
            'description' => $payload['description'] ?? null,
            'status' => $payload['status'] ?? null,
            'payment_method' => $payload['payment_method'] ?? null,
        ];

        if ($fee) {
            $fee->update($data);
        } else {
            $incident->depositFees()->create(['type' => 'vehicle_deposit'] + $data);
        }
    }
}
