<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;

class ActivityLog extends Model
{
    CONST ACTIONS_NAMES = [
        'login' => 'Logowanie',
        'logout' => 'Wylogowanie',
        'created' => 'Dodanie',
        'updated' => 'Aktualizacja',
        'deleted' => 'Usunięcie',
    ];
    
    protected $table = 'activity_log';

    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}