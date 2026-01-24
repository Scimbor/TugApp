<?php

namespace App\Observers;

use App\Models\Incident;
use App\Models\IncidentDepositFee;

class IncidentObserver
{
    public function updated(Incident $incident): void
    {
        if (!$incident->wasChanged('status')) {
            return;
        }

        if ($incident->status !== Incident::STATUS_COMPLETED) {
            return;
        }

        $previousStatus = $incident->getOriginal('status');

        if ($previousStatus === Incident::STATUS_COMPLETED) {
            return;
        }

        $incident->depositFees()->create([
            'fee' => 100.00,
            'description' => 'Naliczono opłatę początkową za depozyt pojazdu',
            'type' => 'vehicle_deposit',
            'status' => 'not_paid',
        ]);
    }
}
