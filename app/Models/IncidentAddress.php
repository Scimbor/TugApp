<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentAddress extends Model
{
    use HasFactory;

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
        return $this->hasOne(Incident::class);
    }
}

