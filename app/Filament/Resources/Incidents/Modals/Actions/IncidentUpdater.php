<?php

namespace App\Filament\Resources\Incidents\Modals\Actions;

use App\Filament\Resources\Incidents\Modals\IncidentModalFormData;
use App\Models\Incident;
use App\Models\IncidentImage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class IncidentUpdater
{
    /**
     * Updates incident from form data (edit modal).
     * Logic: fee only when completed, no changes when closed, otherwise incident+address+images.
     *
     * @param  array<string, mixed>  $data
     */
    public static function updateFromFormData(Incident $incident, array $data): void
    {
        $isClosed = in_array($incident->status ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES);

        if ($incident->status === Incident::STATUS_COMPLETED) {
            $payload = IncidentModalFormData::extractDepositFeePayload($data);
            IncidentModalFormData::saveDepositFee($incident, $payload);

            return;
        }

        if ($isClosed) {
            return;
        }

        if (isset($data['address'])) {
            $addressData = $data['address'];
            $street = isset($addressData['street']) ? trim((string) $addressData['street']) : '';
            if ($incident->address) {
                $incident->address->update($addressData);
            } elseif ($street !== '') {
                $incident->address()->create($addressData);
            }
        }

        self::syncImages($incident, $data['images'] ?? []);

        $modelData = Arr::except($data, ['address', 'depositFee', 'images']);
        $incident->update($modelData);
    }

    /**
     * Syncs images: keep existing in form, remove deleted, add new (from temp).
     *
     * @param  array<int, array{image_path?: string}>  $formImages
     */
    private static function syncImages(Incident $incident, array $formImages): void
    {
        $incidentId = $incident->id;
        $existingPaths = $incident->images()->pluck('image_path')->all();
        $formPaths = array_values(array_filter(array_map(fn ($item) => $item['image_path'] ?? null, $formImages)));

        $toKeep = array_intersect($existingPaths, $formPaths);
        $toDelete = array_diff($existingPaths, $formPaths);
        $toAdd = array_diff($formPaths, $existingPaths);

        foreach ($incident->images as $img) {
            if (in_array($img->image_path, $toDelete, true)) {
                $img->delete();
            }
        }

        foreach ($toAdd as $path) {
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
    }
}
