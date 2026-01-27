<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity, HasApiTokens;

    const ADMIN_ROLE = 'admin';
    const USER_ROLE = 'user';
    const TUG_ROLE = 'tug';

    const ROLES = [
        self::ADMIN_ROLE => 'Admin',
        self::USER_ROLE => 'Użytkownik',
        self::TUG_ROLE => 'Holownik',
    ];

    const PANEL_ROLES = [
        self::ADMIN_ROLE => 'admin',
        self::USER_ROLE => 'user',
    ];

    const MOBILE_ROLES = [
        self::TUG_ROLE => 'tug',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return isset($this->id) && in_array($this->role, self::PANEL_ROLES);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('User Log')
        ->logOnly($this->fillable)
        ->logOnlyDirty();
    }
}
