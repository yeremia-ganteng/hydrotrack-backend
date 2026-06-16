<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WaterLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    // ── Dashboard Utama ───────────────────────────────────────
    public function index()
    {
        $todayWIB = Carbon::now('Asia/Jakarta')->toDateString();
        $users    = User::with(['waterLogs'])->get();
        return view('admin.dashboard', compact('users', 'todayWIB'));
    }

// ── Live Data (polling tiap 3 detik) ─────────────────────
    public function getLiveData()
    {
        // Ambil tanggal hari ini dalam timezone Asia/Jakarta
        $todayWIB = Carbon::now('Asia/Jakarta')->toDateString();

        $users = User::all()->map(function ($user) use ($todayWIB) {
            // Filter sum berdasarkan user_id dan tanggal hari ini
            $total = DB::table('water_logs')
                ->where('user_id', $user->id)
                ->where('date', $todayWIB)
                ->sum('amount');

            return [
                'id'     => $user->id,
                'name'   => $user->name,
                'total'  => (int) $total,
                'target' => $user->daily_target ?? 2000,
            ];
        });

        return response()->json($users);
    }

    // ── Riwayat per User ─────────────────────────────────────
    public function getUserHistory(Request $request, $userId)
    {
        $days = $request->input('days', 7);
        $user = User::with(['waterLogs'])->findOrFail($userId);

        $history = collect(range($days - 1, 0))->map(function ($d) use ($user) {
            $day     = Carbon::now('Asia/Jakarta')->subDays($d);
            $dateStr = $day->toDateString();
            $total   = $user->waterLogs->where('date', $dateStr)->sum('amount');

            return [
                'date'  => $day->translatedFormat('d M'),
                'total' => $total,
            ];
        });

        return response()->json($history);
    }

    // ── Halaman Manajemen Users ───────────────────────────────
    public function users()
    {
        $users = User::with(['waterLogs'])->orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    // ── Hapus User ────────────────────────────────────────────
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->waterLogs()->delete();
        $user->delete();

        return redirect('/admin/users')->with('success', "User \"{$user->name}\" berhasil dihapus.");
    }

    // ── Halaman Log Air ───────────────────────────────────────
    public function logs(Request $request)
    {
        $allLogs = WaterLog::with('user')->orderBy('created_at', 'desc')->get();
        $logs    = WaterLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        $users   = User::orderBy('name')->get();

        return view('admin.logs', compact('logs', 'allLogs', 'users'));
    }

    // ── Halaman Laporan ───────────────────────────────────────
    public function reports()
    {
        $users   = User::with(['waterLogs'])->get();
        $allLogs = WaterLog::with('user')->get();

        return view('admin.reports', compact('users', 'allLogs'));
    }

    // ── Export CSV ────────────────────────────────────────────
    public function exportCsv(): StreamedResponse
    {
        $todayWIB = Carbon::now('Asia/Jakarta')->toDateString();
        $users    = User::with(['waterLogs'])->get();
        $filename = 'water-tracker-report-' . $todayWIB . '.csv';

        return response()->streamDownload(function () use ($users, $todayWIB) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID', 'Nama', 'Email', 'Target Harian (ml)',
                'Konsumsi Hari Ini (ml)', 'Persentase (%)', 'Status',
                'Total Log', 'Total Semua Waktu (ml)', 'Bergabung',
            ]);

            foreach ($users as $user) {
                $todayTotal = $user->waterLogs->where('date', $todayWIB)->sum('amount');
                $target     = $user->daily_target ?? 2000;
                $pct        = $target > 0 ? round(($todayTotal / $target) * 100, 1) : 0;
                $status     = $pct >= 100 ? 'Tercapai' : ($pct < 30 ? 'Rendah' : 'Berjalan');
                $allTime    = $user->waterLogs->sum('amount');

                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $target,
                    $todayTotal,
                    $pct,
                    $status,
                    $user->waterLogs->count(),
                    $allTime,
                    Carbon::parse($user->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}