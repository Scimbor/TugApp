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

    protected function afterCreate(): void
    {
        $incident = $this->record;
        $incidentId = $incident->id;

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
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
