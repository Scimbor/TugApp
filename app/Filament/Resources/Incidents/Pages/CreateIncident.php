<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Filament\Resources\Incidents\IncidentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_panel_id'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Usuń adres z danych przed utworzeniem incydentu
        $addressData = null;
        if (isset($data['address'])) {
            $addressData = $data['address'];
            unset($data['address']);
        }

        // Utwórz incydent
        $incident = parent::handleRecordCreation($data);
        $incidentId = $incident->id;

        // Zapisz adres jeśli został podany
        if ($addressData) {
            $incident->address()->create($addressData);
        }

        // Przenieś zdjęcia do właściwego katalogu
        foreach ($incident->images ?? [] as $image) {
            $currentPath = $image->image_path;
            
            if ($currentPath && !str_contains($currentPath, "incidents_images/{$incidentId}/")) {
                $fileName = basename($currentPath);
                $newPath = "incidents_images/{$incidentId}/{$fileName}";
                
                if (Storage::exists($currentPath)) {
                    Storage::makeDirectory("incidents_images/{$incidentId}");
                    Storage::move($currentPath, $newPath);
                    $image->update(['image_path' => $newPath]);
                } else {
                    $image->update(['image_path' => $newPath]);
                }
            }
        }

        return $incident;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
