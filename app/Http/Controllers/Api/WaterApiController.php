<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaterLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WaterApiController extends Controller
{
    // ── Today Data ────────────────────────────────────────────
    public function getTodayData(Request $request)
    {
        $user   = $request->user();
        $target = $user->daily_target ?? 2000;
        
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $date  = $request->query('date', $today);

        $todayLogs = WaterLog::where('user_id', $user->id)
                            ->where('date', $date) // ← pakai kolom date
                            ->latest()
                            ->get();

        $totalDrunk = $todayLogs->sum('amount');
        $percentage = $target > 0 ? round(($totalDrunk / $target) * 100, 1) : 0;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_drunk' => $totalDrunk,
                'target'      => $target,
                'percentage'  => $percentage,
                'logs'        => $todayLogs,
            ],
        ]);
    }

    // ── Store Water Log ───────────────────────────────────────
    public function storeData(Request $request)
    {
        try {
            $user   = $request->user();
            if (!$user) return response()->json(['message' => 'Unauthenticated.'], 401);

            $amount = $request->input('amount', 250);
            $log    = WaterLog::create([
                'user_id' => $user->id,
                'amount'  => (int)$amount,
                'date'    => Carbon::now('Asia/Jakarta')->toDateString(),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan',
                'data'    => $log,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ── Weekly Stats ──────────────────────────────────────────
public function getWeeklyStats(Request $request)
{
    $user   = $request->user();
    $target = $user->daily_target ?? 2000;

    $startDate = Carbon::now('Asia/Jakarta')->subDays(6)->toDateString();

    $logs = WaterLog::where('user_id', $user->id)
                    ->where('date', '>=', $startDate) // ← pakai kolom date
                    ->get();

    $grouped = [];
    foreach ($logs as $log) {
        $grouped[$log->date] = ($grouped[$log->date] ?? 0) + $log->amount;
    }

    $filled = [];
    for ($i = 6; $i >= 0; $i--) {
        $date     = Carbon::now('Asia/Jakarta')->subDays($i)->format('Y-m-d');
        $filled[] = [
            'day'    => $date,
            'amount' => $grouped[$date] ?? 0,
            'target' => $target,
        ];
    }

    return response()->json(['status' => 'success', 'data' => $filled]);
}

    // update target
    public function updateTarget(Request $request)
    {
        $request->validate([
            'daily_target' => 'required|numeric|min:500',
        ]);

        $user = $request->user();
        $user->daily_target = $request->daily_target;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Target berhasil diupdate',
            'new_target' => $user->daily_target
        ]);
}
}