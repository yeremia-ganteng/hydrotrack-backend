<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Ganti #[Fillable] attribute dengan ini
    protected $fillable = [
        'name',
        'email', 
        'password',
        'daily_target', // ← tambahkan ini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'daily_target'      => 'integer', // ← cast ke integer
        ];
    }

    public function waterLogs()
    {
        return $this->hasMany(WaterLog::class, 'user_id');
    }
}