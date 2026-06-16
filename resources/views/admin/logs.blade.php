<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Air | Water Tracker Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @include('admin.partials.nav-styles')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --accent: #6366f1; --accent2: #a855f7; --green: #10b981;
            --red: #ef4444; --text: #1e1b4b; --muted: #6b7280;
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

        .filters {
            display:flex;align-items:center;gap:10px;flex-wrap:wrap;
        }
        .filter-select {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:12px;
            padding:8px 14px;font-size:13px;font-family:'DM Sans',sans-serif;
            color:var(--text);outline:none;cursor:pointer;
            box-shadow:0 4px 16px var(--shadow);
        }
        .search-bar {
            display:flex;align-items:center;gap:10px;
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:12px;
            padding:8px 14px;box-shadow:0 4px 16px var(--shadow);
        }
        .search-bar input {
            border:none;outline:none;background:transparent;
            font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);width:180px;
        }
        .search-bar input::placeholder { color:#9ca3af; }

        .stats-mini {
            display:grid;grid-template-columns:repeat(4,1fr);gap:14px;
            margin-bottom:24px;
        }
        .stat-mini {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:16px;
            padding:16px 20px;box-shadow:0 4px 20px var(--shadow);
            opacity:0;transform:translateY(16px);
            animation:cardUp 0.5s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes cardUp { to { opacity:1;transform:translateY(0); } }
        .stat-mini-val { font-size:20px;font-weight:800;letter-spacing:-0.5px;color:var(--accent); }
        .stat-mini-lbl { font-size:11px;color:var(--muted);font-weight:500;margin-top:3px; }

        .table-card {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:24px;
            box-shadow:0 8px 32px var(--shadow);overflow:hidden;
            opacity:0;transform:translateY(20px);
            animation:cardUp 0.55s cubic-bezier(0.22,1,0.36,1) 0.25s forwards;
        }
        table { width:100%;border-collapse:collapse; }
        thead th {
            padding:14px 20px;text-align:left;
            font-size:11px;font-weight:700;color:var(--muted);
            letter-spacing:0.5px;text-transform:uppercase;
            background:rgba(99,102,241,0.04);
            border-bottom:1px solid rgba(99,102,241,0.1);
        }
        tbody tr { border-bottom:1px solid rgba(99,102,241,0.06);transition:background 0.2s; }
        tbody tr:last-child { border-bottom:none; }
        tbody tr:hover { background:rgba(99,102,241,0.04); }
        tbody td { padding:13px 20px;font-size:13px;vertical-align:middle; }

        .avatar-sm {
            width:32px;height:32px;border-radius:9px;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            display:flex;align-items:center;justify-content:center;
            font-size:13px;font-weight:800;color:white;flex-shrink:0;
        }
        .user-cell { display:flex;align-items:center;gap:10px; }

        .amount-badge {
            display:inline-flex;align-items:center;gap:4px;
            font-weight:700;font-size:13px;
            background:rgba(99,102,241,0.08);color:var(--accent);
            padding:4px 10px;border-radius:8px;
            font-family:'DM Mono',monospace;
        }
        .amount-badge.lg { background:rgba(16,185,129,0.1);color:#059669; }

        .pagination {
            display:flex;align-items:center;justify-content:space-between;
            padding:16px 20px;border-top:1px solid rgba(99,102,241,0.08);
            font-size:13px;color:var(--muted);
        }
        .pagination-links { display:flex;gap:6px; }
        .pagination-links a, .pagination-links span {
            padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;
            text-decoration:none;color:var(--muted);background:rgba(99,102,241,0.06);
            transition:background 0.2s,color 0.2s;
        }
        .pagination-links a:hover { background:rgba(99,102,241,0.14);color:var(--accent); }
        .pagination-links .active-page { background:var(--accent);color:white; }

        @media(max-width:768px) {
            .stats-mini { grid-template-columns:repeat(2,1fr); }
            .col-hide { display:none; }
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
        $totalLogs   = $logs->total();
        $totalMl     = $allLogs->sum('amount');
        $todayLogs   = $allLogs->filter(fn($l) =>
            Carbon::parse($l->created_at)->setTimezone('Asia/Jakarta')->toDateString() === Carbon::now('Asia/Jakarta')->toDateString()
        )->count();
        $avgPerLog   = $allLogs->count() > 0 ? round($allLogs->avg('amount')) : 0;
    @endphp

    <div class="page-header">
        <div>
            <div class="page-title">💧 Log Air</div>
            <div class="page-sub">Semua riwayat konsumsi air pengguna</div>
        </div>
        <div class="filters">
            <div class="search-bar">
                <span>🔍</span>
                <input type="text" placeholder="Cari nama..." id="searchLog" onkeyup="filterLogs()">
            </div>
            <select class="filter-select" id="userFilter" onchange="filterLogs()">
                <option value="">Semua User</option>
                @foreach($users as $u)
                    <option value="{{ strtolower($u->name) }}">{{ $u->name }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="dateFilter" onchange="filterLogs()">
                <option value="">Semua Tanggal</option>
                <option value="today">Hari Ini</option>
                <option value="week">7 Hari Terakhir</option>
            </select>
        </div>
    </div>

    <!-- Mini Stats -->
    <div class="stats-mini">
        <div class="stat-mini" style="animation-delay:.1s">
            <div class="stat-mini-val">{{ number_format($totalLogs) }}</div>
            <div class="stat-mini-lbl">Total Log</div>
        </div>
        <div class="stat-mini" style="animation-delay:.15s">
            <div class="stat-mini-val">{{ number_format($totalMl) }} ml</div>
            <div class="stat-mini-lbl">Total Konsumsi (semua waktu)</div>
        </div>
        <div class="stat-mini" style="animation-delay:.2s">
            <div class="stat-mini-val">{{ $todayLogs }}</div>
            <div class="stat-mini-lbl">Log Hari Ini</div>
        </div>
        <div class="stat-mini" style="animation-delay:.25s">
            <div class="stat-mini-val">{{ number_format($avgPerLog) }} ml</div>
            <div class="stat-mini-lbl">Rata-rata per Log</div>
        </div>
    </div>

    <div class="table-card">
        <table id="logsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengguna</th>
                    <th>Jumlah</th>
                    <th class="col-hide">Tanggal</th>
                    <th class="col-hide">Jam (WIB)</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                @php
                    $wib      = Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta');
                    $isToday  = $wib->toDateString() === Carbon::now('Asia/Jakarta')->toDateString();
                    $isLarge  = $log->amount >= 400;
                @endphp
                <tr
                    data-name="{{ strtolower($log->user->name ?? '') }}"
                    data-date="{{ $wib->toDateString() }}"
                    data-today="{{ $isToday ? 'today' : '' }}"
                    data-week="{{ $wib->gte(Carbon::now('Asia/Jakarta')->subDays(7)) ? 'week' : '' }}"
                >
                    <td style="color:var(--muted);font-family:'DM Mono',monospace;font-size:11px;">
                        {{ $logs->firstItem() + $i }}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="avatar-sm">{{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}</div>
                            <span style="font-weight:600;">{{ $log->user->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="amount-badge {{ $isLarge ? 'lg' : '' }}">
                            💧 {{ number_format($log->amount) }} ml
                        </span>
                    </td>
                    <td class="col-hide" style="color:var(--muted);font-size:12px;">
                        {{ $wib->format('d M Y') }}
                    </td>
                    <td class="col-hide" style="font-family:'DM Mono',monospace;font-size:12px;color:var(--accent);">
                        {{ $wib->format('H:i:s') }}
                    </td>
                    <td style="font-size:12px;color:var(--muted);">
                        {{ $isToday ? '🟢 ' : '' }}{{ $wib->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:60px;color:var(--muted);">
                        <div style="font-size:48px;margin-bottom:12px;">💧</div>
                        <div>Belum ada log air</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="pagination">
            <div>Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log</div>
            <div class="pagination-links">
                @if($logs->onFirstPage())
                    <span>‹</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}">‹</a>
                @endif

                @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                    @if($page == $logs->currentPage())
                        <span class="active-page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}">›</a>
                @else
                    <span>›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function filterLogs() {
        const search = document.getElementById('searchLog').value.toLowerCase();
        const user   = document.getElementById('userFilter').value;
        const date   = document.getElementById('dateFilter').value;

        document.querySelectorAll('#logsTable tbody tr[data-name]').forEach(row => {
            const matchName = row.dataset.name.includes(search);
            const matchUser = !user || row.dataset.name === user;
            const matchDate = !date ||
                (date === 'today' && row.dataset.today === 'today') ||
                (date === 'week'  && row.dataset.week  === 'week');
            row.style.display = (matchName && matchUser && matchDate) ? '' : 'none';
        });
    }
</script>
</body>
</html>