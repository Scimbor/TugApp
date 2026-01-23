<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Filament\Resources\Incidents\IncidentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
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

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
}
