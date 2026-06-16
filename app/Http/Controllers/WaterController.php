<?php

namespace App\Http\Controllers;

use App\Models\WaterLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WaterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $date = $request->query('date', $today);

        $todayLogs = WaterLog::where('user_id', $user->id)
                             ->where('date', $date) // ← pakai kolom date + WIB
                             ->latest()
                             ->get();

        $totalDrunk = $todayLogs->sum('amount');
        $target = $user->daily_target ?? 2000;
        $percentage = $target > 0 ? round(($totalDrunk / $target) * 100, 2) : 0;

        return response()->json([
            'total_drunk' => $totalDrunk,
            'target'      => $target,
            'percentage'  => $percentage,
            'logs'        => $todayLogs,
            'date'        => $date,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'amount' => 'required|integer|min:1'
        ]);

        $nowWib = Carbon::now('Asia/Jakarta');

        WaterLog::create([
            'user_id'    => $user->id,
            'amount'     => $request->amount,
            'date'       => $nowWib->toDateString(),
            'created_at' => $nowWib,
            'updated_at' => $nowWib,
        ]);

        return response()->json(['message' => 'Berhasil'], 201);
    }
}