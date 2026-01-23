<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentAddress extends Model
{
    protected $table = 'incidents_address';

    protected $fillable = [
        'incident_id',
        'street',
        'house_number',
        'apartment_number',
        'city',
        'zip',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}

