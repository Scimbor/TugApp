<?php

namespace App\Filament\Resources\Incidents\Modals;

use App\Models\Incident;
use App\Models\IncidentDepositFee;

class IncidentModalFormData
{
    /**
     * Data to fill the edit form (address, deposit fee, images).
     *
     * @return array<string, mixed>
     */
    public static function fillForEdit(Incident $incident): array
    {
        $incident->loadMissing(['address', 'depositFees', 'images']);

        $data = [];

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

        $images = $incident->images()->orderBy('id')->get();
        if ($images->isNotEmpty()) {
            $data['images'] = $images->map(fn ($img) => ['image_path' => $img->image_path])->all();
        }

        return $data;
    }

    /**
     * Extracts deposit fee payload from form data (nested or flat).
     *
     * @param  array<string, mixed>  $data
     * @return array{fee: float, description: mixed, status: mixed, payment_method: mixed}
     */
    public static function extractDepositFeePayload(array $data): array
    {
        $nested = data_get($data, 'depositFee');
        if (is_array($nested)) {
            return [
                'fee' => (float) ($nested['fee'] ?? 0),
                'description' => $nested['description'] ?? null,
                'status' => $nested['status'] ?? null,
                'payment_method' => $nested['payment_method'] ?? null,
            ];
        }

        return [
            'fee' => (float) (data_get($data, 'depositFee.fee') ?? $data['depositFee.fee'] ?? 0),
            'description' => data_get($data, 'depositFee.description') ?? $data['depositFee.description'] ?? null,
            'status' => data_get($data, 'depositFee.status') ?? $data['depositFee.status'] ?? null,
            'payment_method' => data_get($data, 'depositFee.payment_method') ?? $data['depositFee.payment_method'] ?? null,
        ];
    }

    public static function saveDepositFee(Incident $incident, array $payload): void
    {
        $fee = $incident->depositFees()->first();

        if ($fee && in_array($fee->status ?? null, IncidentDepositFee::CLOSED_MODIFICATION_ROW_STATUSES)) {
            return;
        }

        $attrs = [
            'fee' => (float) ($payload['fee'] ?? 0),
            'description' => $payload['description'] ?? null,
            'status' => $payload['status'] ?? null,
            'payment_method' => $payload['payment_method'] ?? null,
        ];

        if ($fee) {
            $fee->update($attrs);
        } else {
            $incident->depositFees()->create(['type' => 'vehicle_deposit'] + $attrs);
        }
    }
}
