<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterLog;
use Carbon\Carbon;

class WaterLogController extends Controller
{
public function index(Request $request)
{
    $user = $request->user();
    $today = Carbon::now('Asia/Jakarta')->toDateString();
    $date = $request->query('date', $today);

    // Filter pakai kolom 'date' yang sudah ada di tabel
    $logs = $user->waterLogs()
                 ->where('date', $date)  // ← pakai kolom date langsung
                 ->latest()
                 ->get();

    $totalDrunk = $logs->sum('amount');
    $target = $user->daily_target ?? 2000;

    return response()->json([
        'total_drunk' => $totalDrunk,
        'target'      => $target,
        'percentage'  => $target > 0 ? round(($totalDrunk / $target) * 100, 2) : 0,
        'logs'        => $logs,
        'date'        => $date,
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'amount' => 'required|integer|min:1',
    ]);

    $nowWib = Carbon::now('Asia/Jakarta');

    $log = $request->user()->waterLogs()->create([
        'amount'     => $request->amount,
        'date'       => $nowWib->toDateString(), // ← pastikan date terisi WIB
        'created_at' => $nowWib,
        'updated_at' => $nowWib,
    ]);

    return response()->json([
        'message' => 'Data berhasil ditambahkan',
        'data'    => $log,
    ], 201);
}
}