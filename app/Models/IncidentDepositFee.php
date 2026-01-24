<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentDepositFee extends Model
{
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_NOT_PAID = 'not_paid';
    const PAYMENT_STATUS_CANCELLED = 'cancelled';

    const PAYMENT_STATUS_OPTIONS = [
        self::PAYMENT_STATUS_PAID => 'Opłacony',
        self::PAYMENT_STATUS_NOT_PAID => 'Nieopłacony',
        self::PAYMENT_STATUS_CANCELLED => 'Anulowany',
    ];
    
    const CLOSED_MODIFICATION_ROW_STATUSES = [
        self::PAYMENT_STATUS_PAID,
        self::PAYMENT_STATUS_CANCELLED,
    ];

    protected $table = 'incidents_deposits_fee';

    protected $fillable = [
        'incident_id',
        'fee',
        'description',
        'type',
        'status',
        'payment_method',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
