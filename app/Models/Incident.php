<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ON_HOLD = 'on_hold';
    
    const STATUS_OPTIONS = [
        self::STATUS_OPEN => 'Otwarty',
        self::STATUS_CLOSED => 'Zakończony',
        self::STATUS_IN_PROGRESS => 'W realizacji',
        self::STATUS_PENDING => 'Oczekujący',
        self::STATUS_CANCELLED => 'Odowłany',
        self::STATUS_COMPLETED => 'Zakończony',
        self::STATUS_ON_HOLD => 'Wstrzymany',
    ];

    const STATUS_COLORS = [
        self::STATUS_OPEN => 'warning',
        self::STATUS_CLOSED => 'success',
        self::STATUS_IN_PROGRESS => 'info',
        self::STATUS_PENDING => 'warning',
        self::STATUS_CANCELLED => 'danger',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_ON_HOLD => 'warning',
    ];

    protected $table = 'incidents';
    protected $fillable = ['user_panel_id', 'vehicle_number', 'vehicle_vin', 'vehicle_brand', 'vehicle_model', 'vehicle_type', 'description', 'status'];
}