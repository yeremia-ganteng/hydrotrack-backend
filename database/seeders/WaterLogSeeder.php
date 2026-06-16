<?php

namespace Database\Seeders;

use App\Models\WaterLog;
use App\Models\User; // Tambahkan ini
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WaterLogSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user pertama agar data memiliki pemilik
        $user = User::first();

        if ($user) {
            // Contoh catatan minum hari ini
            WaterLog::create([
                'user_id' => $user->id, // Tambahkan user_id
                'amount' => 250,
                'created_at' => Carbon::today(), // Gunakan created_at
            ]);

            WaterLog::create([
                'user_id' => $user->id,
                'amount' => 500,
                'created_at' => Carbon::today(),
            ]);

            // Contoh catatan minum kemarin
            WaterLog::create([
                'user_id' => $user->id,
                'amount' => 250,
                'created_at' => Carbon::yesterday(),
            ]);
        }
    }
}