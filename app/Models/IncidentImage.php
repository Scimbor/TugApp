<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IncidentImage extends Model
{
    use LogsActivity;

    protected $table = 'incidents_images';
    protected $fillable = ['incident_id', 'image_path'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($incidentImage) {
            if ($incidentImage->image_path && Storage::exists($incidentImage->image_path)) {
                Storage::delete($incidentImage->image_path);
            }
        });
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('Incident Image Log')
        ->logOnly($this->fillable)
        ->logOnlyDirty(); 
    }
}