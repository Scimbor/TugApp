<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;

class Incident extends Model
{
    use LogsActivity;

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ON_HOLD = 'on_hold';
    
    const STATUS_OPTIONS = [
        self::STATUS_OPEN => 'Otwarty',
        self::STATUS_CLOSED => 'Zamknięty',
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

    const CLOSED_MODIFICATION_ROW_STATUSES = [
        self::STATUS_COMPLETED, 
        self::STATUS_CLOSED, 
        self::STATUS_CANCELLED,
    ];

    protected $table = 'incidents';
    protected $fillable = ['user_panel_id', 'vehicle_number', 'vehicle_vin', 'vehicle_brand', 'vehicle_model', 'vehicle_type', 'description', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($incident) {
            $images = $incident->images;
            
            foreach ($images as $image) {
                if ($image->image_path && Storage::exists($image->image_path)) {
                    Storage::delete($image->image_path);
                }
            }

            $directoryPath = "incidents_images/{$incident->id}";
            
            if (Storage::exists($directoryPath)) {
                Storage::deleteDirectory($directoryPath);
            }
        });
    }

    public function images()
    {
        return $this->hasMany(IncidentImage::class);
    }

    public function address()
    {
        return $this->hasOne(IncidentAddress::class);
    }

    public function depositFees()
    {
        return $this->hasMany(IncidentDepositFee::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('Incident Log')
        ->logOnly($this->fillable)
        ->logOnlyDirty(); 
    }
}