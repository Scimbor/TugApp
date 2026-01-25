<?php

namespace App\Filament\Resources\Incidents\Modals\Actions;

use App\Models\Incident;
use App\Models\IncidentImage;
use Illuminate\Support\Facades\Storage;

class IncidentCreator
{
    /**
     * Creates incident from form data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function createFromFormData(array $data): Incident
    {
        $data['user_panel_id'] = auth()->id();

        $addressData = null;
        if (isset($data['address'])) {
            $addressData = $data['address'];
            unset($data['address']);
        }

        $imagesData = $data['images'] ?? [];
        unset($data['images']);
        unset($data['depositFee']);

        $incident = Incident::create($data);
        $incidentId = $incident->id;

        $street = isset($addressData['street']) ? trim((string) $addressData['street']) : '';
        if ($addressData && $street !== '') {
            $incident->address()->create($addressData);
        }

        foreach ($imagesData as $item) {
            $path = $item['image_path'] ?? null;
            if (!$path) {
                continue;
            }
            $incident->images()->create(['image_path' => $path]);
        }

        $incident->load('images');

        foreach ($incident->images as $image) {
            $currentPath = $image->image_path;
            if (!$currentPath || str_contains($currentPath, IncidentImage::IMAGES_DIRECTORY . "/{$incidentId}/")) {
                continue;
            }
            $fileName = basename($currentPath);
            $newPath = IncidentImage::IMAGES_DIRECTORY . "/{$incidentId}/{$fileName}";
            if (Storage::exists($currentPath)) {
                Storage::makeDirectory(IncidentImage::IMAGES_DIRECTORY . "/{$incidentId}");
                Storage::move($currentPath, $newPath);
                $image->update(['image_path' => $newPath]);
            } else {
                $image->update(['image_path' => $newPath]);
            }
        }

        return $incident;
    }
}
