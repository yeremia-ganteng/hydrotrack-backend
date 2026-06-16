<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WaterLog extends Model
{
    protected $fillable = ['user_id', 'amount', 'date', 'created_at']; // ← tambah 'date'

    // Auto-isi kolom date saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->date)) {
                $log->date = Carbon::parse($log->created_at ?? now())
                    ->setTimezone('Asia/Jakarta')
                    ->toDateString();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}