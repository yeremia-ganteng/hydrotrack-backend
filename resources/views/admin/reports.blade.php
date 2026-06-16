<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan | Water Tracker Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @include('admin.partials.nav-styles')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --accent: #6366f1; --accent2: #a855f7; --accent3: #ec4899;
            --green: #10b981; --red: #ef4444; --text: #1e1b4b; --muted: #6b7280;
            --surface: rgba(255,255,255,0.72); --border: rgba(255,255,255,0.92);
            --shadow: rgba(99,102,241,0.14);
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 50%, #fce7f3 100%);
            min-height: 100vh; color: var(--text);
        }
        .orb { position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0; }
        .orb1 { width:500px;height:500px;top:-100px;left:-150px;background:rgba(99,102,241,0.10); }
        .orb2 { width:400px;height:400px;bottom:-80px;right:-100px;background:rgba(236,72,153,0.08); }

        .wrap { position:relative;z-index:1;max-width:1400px;margin:0 auto;padding:32px; }

        .page-header {
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:28px;flex-wrap:wrap;gap:12px;
            opacity:0;transform:translateY(-16px);
            animation:slideDown 0.5s cubic-bezier(0.22,1,0.36,1) 0.1s forwards;
        }
        @keyframes slideDown { to { opacity:1;transform:translateY(0); } }
        .page-title { font-size:22px;font-weight:800;letter-spacing:-0.5px;color:var(--accent); }
        .page-sub   { font-size:13px;color:var(--muted);margin-top:3px; }

        .btn-export {
            display:flex;align-items:center;gap:8px;
            background:linear-gradient(135deg,var(--green),#34d399);
            color:white;border:none;border-radius:12px;
            padding:10px 20px;font-size:13px;font-weight:700;
            font-family:'DM Sans',sans-serif;cursor:pointer;
            box-shadow:0 4px 16px rgba(16,185,129,0.3);
            transition:transform 0.2s,box-shadow 0.2s;text-decoration:none;
        }
        .btn-export:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(16,185,129,0.4); }

        .stats-row {
            display:grid;grid-template-columns:repeat(4,1fr);gap:16px;
            margin-bottom:28px;
        }
        .stat-card {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:20px;
            padding:20px 22px;box-shadow:0 4px 24px var(--shadow);
            opacity:0;transform:translateY(20px);
            animation:cardUp 0.55s cubic-bezier(0.22,1,0.36,1) both;
            transition:transform 0.3s,box-shadow 0.3s;
        }
        .stat-card:hover { transform:translateY(-4px);box-shadow:0 12px 36px rgba(99,102,241,0.2); }
        @keyframes cardUp { to { opacity:1;transform:translateY(0); } }
        .stat-icon { font-size:24px;margin-bottom:10px; }
        .stat-val  { font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--accent); }
        .stat-lbl  { font-size:12px;color:var(--muted);font-weight:500;margin-top:4px; }

        .charts-grid { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px; }
        .chart-card {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:24px;
            padding:24px;box-shadow:0 8px 32px var(--shadow);
            opacity:0;transform:translateY(20px);
            animation:cardUp 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }
        .chart-card-full {
            grid-column: 1 / -1;
        }
        .chart-title { font-size:15px;font-weight:700;margin-bottom:4px; }
        .chart-sub   { font-size:12px;color:var(--muted);margin-bottom:20px; }
        .chart-wrap  { height:240px; }

        .table-card {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:24px;
            box-shadow:0 8px 32px var(--shadow);overflow:hidden;
            opacity:0;transform:translateY(20px);
            animation:cardUp 0.55s cubic-bezier(0.22,1,0.36,1) 0.4s forwards;
        }
        .table-title {
            padding:20px 24px 0;font-size:15px;font-weight:700;
        }
        .table-sub { padding:4px 24px 16px;font-size:12px;color:var(--muted); }
        table { width:100%;border-collapse:collapse; }
        thead th {
            padding:12px 20px;text-align:left;
            font-size:11px;font-weight:700;color:var(--muted);
            letter-spacing:0.5px;text-transform:uppercase;
            background:rgba(99,102,241,0.04);
            border-bottom:1px solid rgba(99,102,241,0.1);
        }
        tbody tr { border-bottom:1px solid rgba(99,102,241,0.06);transition:background 0.2s; }
        tbody tr:last-child { border-bottom:none; }
        tbody tr:hover { background:rgba(99,102,241,0.04); }
        tbody td { padding:13px 20px;font-size:13px;vertical-align:middle; }

        .rank-badge {
            width:28px;height:28px;border-radius:8px;
            display:inline-flex;align-items:center;justify-content:center;
            font-size:12px;font-weight:800;
        }
        .rank-1 { background:linear-gradient(135deg,#f59e0b,#fbbf24);color:white; }
        .rank-2 { background:linear-gradient(135deg,#6b7280,#9ca3af);color:white; }
        .rank-3 { background:linear-gradient(135deg,#b45309,#d97706);color:white; }
        .rank-n { background:rgba(99,102,241,0.08);color:var(--muted); }

        .progress-mini { display:flex;align-items:center;gap:8px; }
        .bar-mini { flex:1;height:5px;background:rgba(99,102,241,0.1);border-radius:100px;overflow:hidden; }
        .bar-mini-fill { height:100%;border-radius:100px;background:linear-gradient(90deg,var(--accent),var(--accent2)); }

        @media(max-width:900px) {
            .charts-grid { grid-template-columns:1fr; }
            .chart-card-full { grid-column:1; }
            .stats-row { grid-template-columns:repeat(2,1fr); }
            .wrap { padding:16px; }
        }
    </style>
</head>
<body>

<div class="orb orb1"></div>
<div class="orb orb2"></div>

@include('admin.partials.navbar')

<div class="wrap">

    @php
        use Carbon\Carbon;
        $todayWIB    = Carbon::now('Asia/Jakarta')->toDateString();
        $totalUsers  = $users->count();
        $totalMlAll  = $allLogs->sum('amount');
        $todayMl     = $allLogs->filter(fn($l) =>
            Carbon::parse($l->created_at)->setTimezone('Asia/Jakarta')->toDateString() === $todayWIB
        )->sum('amount');
        $doneToday   = $users->filter(fn($u) =>
            $u->waterlogs->filter(fn($l) =>
                Carbon::parse($l->created_at)->setTimezone('Asia/Jakarta')->toDateString() === $todayWIB
            )->sum('amount') >= ($u->daily_target ?? 2000)
        )->count();
        $avgDaily    = $totalUsers > 0 ? round($todayMl / $totalUsers) : 0;

        // 7 hari terakhir — total semua user per hari
        $days7 = collect(range(6,0))->map(fn($d) => Carbon::now('Asia/Jakarta')->subDays($d));
        $daily7Labels = $days7->map(fn($d) => $d->format('d M'))->values()->toArray();
        $daily7Data   = $days7->map(fn($day) =>
            $allLogs->filter(fn($l) =>
                Carbon::parse($l->created_at)->setTimezone('Asia/Jakarta')->toDateString() === $day->toDateString()
            )->sum('amount')
        )->values()->toArray();

        // Leaderboard — total konsumsi hari ini per user
        $leaderboard = $users->map(fn($u) => [
            'name'  => $u->name,
            'total' => $u->waterlogs->filter(fn($l) =>
                Carbon::parse($l->created_at)->setTimezone('Asia/Jakarta')->toDateString() === $todayWIB
            )->sum('amount'),
            'target' => $u->daily_target ?? 2000,
        ])->sortByDesc('total')->values();

        $maxTotal = $leaderboard->max('total') ?: 1;

        // Distribusi jumlah minuman (per rentang ml)
        $dist = [
            '< 200ml'    => $allLogs->where('amount', '<', 200)->count(),
            '200–400ml'  => $allLogs->whereBetween('amount', [200, 399])->count(),
            '400–600ml'  => $allLogs->whereBetween('amount', [400, 599])->count(),
            '≥ 600ml'    => $allLogs->where('amount', '>=', 600)->count(),
        ];
    @endphp

    <div class="page-header">
        <div>
            <div class="page-title">📈 Laporan</div>
            <div class="page-sub">Statistik dan analitik konsumsi air</div>
        </div>
        <a href="/admin/reports/export-csv" class="btn-export">
            ⬇️ Export CSV
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card" style="animation-delay:.1s">
            <div class="stat-icon">👥</div>
            <div class="stat-val">{{ $totalUsers }}</div>
            <div class="stat-lbl">Total Pengguna</div>
        </div>
        <div class="stat-card" style="animation-delay:.15s">
            <div class="stat-icon">💧</div>
            <div class="stat-val">{{ number_format($todayMl) }} ml</div>
            <div class="stat-lbl">Total Konsumsi Hari Ini</div>
        </div>
        <div class="stat-card" style="animation-delay:.2s">
            <div class="stat-icon">✅</div>
            <div class="stat-val">{{ $doneToday }} / {{ $totalUsers }}</div>
            <div class="stat-lbl">Target Tercapai Hari Ini</div>
        </div>
        <div class="stat-card" style="animation-delay:.25s">
            <div class="stat-icon">📊</div>
            <div class="stat-val">{{ number_format($avgDaily) }} ml</div>
            <div class="stat-lbl">Rata-rata per User Hari Ini</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-card chart-card-full" style="animation-delay:.2s">
            <div class="chart-title">Tren Konsumsi 7 Hari Terakhir</div>
            <div class="chart-sub">Total konsumsi air semua pengguna per hari</div>
            <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
        </div>

        <div class="chart-card" style="animation-delay:.25s">
            <div class="chart-title">Distribusi Ukuran Minuman</div>
            <div class="chart-sub">Berdasarkan semua log yang tercatat</div>
            <div class="chart-wrap"><canvas id="distChart"></canvas></div>
        </div>

        <div class="chart-card" style="animation-delay:.3s">
            <div class="chart-title">Pencapaian Target Hari Ini</div>
            <div class="chart-sub">Persentase user yang mencapai target</div>
            <div class="chart-wrap"><canvas id="achieveChart"></canvas></div>
        </div>
    </div>

    <!-- Leaderboard -->
    <div class="table-card">
        <div class="table-title">🏆 Leaderboard Hari Ini</div>
        <div class="table-sub">Peringkat konsumsi air pengguna hari ini</div>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Pengguna</th>
                    <th>Progress</th>
                    <th>Total</th>
                    <th>Target</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaderboard as $i => $row)
                @php
                    $pct    = min(round(($row['total'] / $row['target']) * 100), 100);
                    $rankCls = match($i) { 0=>'rank-1', 1=>'rank-2', 2=>'rank-3', default=>'rank-n' };
                    $rankLbl = match($i) { 0=>'🥇', 1=>'🥈', 2=>'🥉', default=>$i+1 };
                @endphp
                <tr>
                    <td><span class="rank-badge {{ $rankCls }}">{{ $rankLbl }}</span></td>
                    <td style="font-weight:600;">{{ $row['name'] }}</td>
                    <td>
                        <div class="progress-mini">
                            <div class="bar-mini">
                                <div class="bar-mini-fill" style="width:{{ $pct }}%;{{ $pct>=100 ? 'background:linear-gradient(90deg,#10b981,#34d399)' : '' }}"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:var(--accent);min-width:36px;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;">{{ number_format($row['total']) }} ml</td>
                    <td style="font-size:12px;color:var(--muted);">{{ number_format($row['target']) }} ml</td>
                    <td>
                        @if($pct >= 100)
                            <span style="font-size:12px;font-weight:700;color:#059669;background:rgba(16,185,129,0.1);padding:3px 10px;border-radius:100px;">✅ Tercapai</span>
                        @elseif($pct < 30)
                            <span style="font-size:12px;font-weight:700;color:#dc2626;background:rgba(239,68,68,0.1);padding:3px 10px;border-radius:100px;">⚠️ Rendah</span>
                        @else
                            <span style="font-size:12px;font-weight:700;color:var(--accent);background:rgba(99,102,241,0.1);padding:3px 10px;border-radius:100px;">🔵 Berjalan</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
const accentColor  = 'rgba(99,102,241,';
const purpleColor  = 'rgba(168,85,247,';
const greenColor   = 'rgba(16,185,129,';
const redColor     = 'rgba(239,68,68,';

// Trend Chart
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($daily7Labels),
        datasets: [{
            label: 'Total (ml)',
            data: @json($daily7Data),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#6366f1',
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(255,255,255,0.95)',
                titleColor: '#6366f1', bodyColor: '#374151',
                borderColor: 'rgba(99,102,241,0.2)', borderWidth: 1, padding: 10,
                callbacks: { label: ctx => ` ${ctx.parsed.y.toLocaleString('id')} ml` }
            }
        },
        scales: {
            x: { ticks: { color: '#9ca3af', font: { size: 11, family: 'DM Mono' } }, grid: { display: false }, border: { display: false } },
            y: { ticks: { color: '#9ca3af', font: { size: 11 }, maxTicksLimit: 5 }, grid: { color: 'rgba(99,102,241,0.06)' }, border: { display: false } }
        }
    }
});

// Distribution Donut
new Chart(document.getElementById('distChart'), {
    type: 'doughnut',
    data: {
        labels: @json(array_keys($dist)),
        datasets: [{
            data: @json(array_values($dist)),
            backgroundColor: [
                'rgba(99,102,241,0.75)',
                'rgba(168,85,247,0.75)',
                'rgba(236,72,153,0.75)',
                'rgba(16,185,129,0.75)',
            ],
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
            legend: { position: 'right', labels: { font: { size: 12, family: 'DM Sans' }, color: '#6b7280', padding: 16, boxWidth: 12, borderRadius: 4 } },
            tooltip: {
                backgroundColor: 'rgba(255,255,255,0.95)',
                titleColor: '#6366f1', bodyColor: '#374151',
                borderColor: 'rgba(99,102,241,0.2)', borderWidth: 1, padding: 10,
                callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} log` }
            }
        }
    }
});

// Achievement Pie
new Chart(document.getElementById('achieveChart'), {
    type: 'doughnut',
    data: {
        labels: ['Target Tercapai', 'Belum Tercapai'],
        datasets: [{
            data: [{{ $doneToday }}, {{ $totalUsers - $doneToday }}],
            backgroundColor: ['rgba(16,185,129,0.8)', 'rgba(239,68,68,0.15)'],
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
            legend: { position: 'right', labels: { font: { size: 12, family: 'DM Sans' }, color: '#6b7280', padding: 16, boxWidth: 12 } },
            tooltip: {
                backgroundColor: 'rgba(255,255,255,0.95)',
                titleColor: '#6366f1', bodyColor: '#374151',
                borderColor: 'rgba(99,102,241,0.2)', borderWidth: 1, padding: 10,
                callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} user` }
            }
        }
    }
});
</script>
</body>
</html>