<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Monitoring | Water Tracker</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @include('admin.partials.nav-styles')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:  #6366f1;
            --accent2: #a855f7;
            --accent3: #ec4899;
            --green:   #10b981;
            --red:     #ef4444;
            --text:    #1e1b4b;
            --muted:   #6b7280;
            --surface: rgba(255,255,255,0.72);
            --border:  rgba(255,255,255,0.92);
            --shadow:  rgba(99,102,241,0.14);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 50%, #fce7f3 100%);
            min-height: 100vh; color: var(--text); overflow-x: hidden;
        }

        .orb {
            position: fixed; border-radius: 50%;
            filter: blur(80px); pointer-events: none; z-index: 0;
            animation: orbFloat 8s ease-in-out infinite;
        }
        .orb1 { width:500px;height:500px;top:-100px;left:-150px;background:rgba(99,102,241,0.12);animation-delay:0s; }
        .orb2 { width:400px;height:400px;bottom:-80px;right:-100px;background:rgba(236,72,153,0.10);animation-delay:3s; }
        .orb3 { width:300px;height:300px;top:40%;left:45%;background:rgba(168,85,247,0.08);animation-delay:6s; }
        @keyframes orbFloat {
            0%,100% { transform:translate(0,0) scale(1); }
            33%     { transform:translate(20px,-30px) scale(1.05); }
            66%     { transform:translate(-15px,20px) scale(0.97); }
        }

        .wrap { position:relative;z-index:1;max-width:1400px;margin:0 auto;padding:32px 32px 40px; }

        /* Page header bawah navbar */
        .page-header {
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:28px;
            opacity:0;transform:translateY(-16px);
            animation:slideDown 0.5s cubic-bezier(0.22,1,0.36,1) 0.1s forwards;
        }
        @keyframes slideDown { to { opacity:1;transform:translateY(0); } }
        .page-title { font-size:20px;font-weight:800;letter-spacing:-0.4px;color:var(--accent); }
        .page-sub   { font-size:13px;color:var(--muted);margin-top:3px; }
        .header-right { display:flex;flex-direction:column;align-items:flex-end;gap:6px; }
        .live-badge {
            display:flex;align-items:center;gap:8px;
            background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);
            border-radius:100px;padding:6px 14px;
            font-size:12px;font-weight:600;color:var(--green);font-family:'DM Mono',monospace;
        }
        .live-dot {
            width:7px;height:7px;border-radius:50%;background:var(--green);
            animation:pulse 1.8s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity:1;transform:scale(1);box-shadow:0 0 0 0 rgba(16,185,129,0.4); }
            50%      { opacity:.5;transform:scale(.7);box-shadow:0 0 0 6px rgba(16,185,129,0); }
        }
        .wib-time { font-family:'DM Mono',monospace;font-size:12px;color:var(--muted); }
        .wib-time span { color:var(--accent2);font-weight:600; }

        /* Stats */
        .stats-bar { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:32px; }
        .stat-card {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:20px;
            padding:20px 24px;display:flex;align-items:center;gap:16px;
            box-shadow:0 4px 24px var(--shadow);
            opacity:0;transform:translateY(24px);
            animation:cardUp 0.6s cubic-bezier(0.22,1,0.36,1) both;
            transition:transform 0.3s ease,box-shadow 0.3s ease;cursor:default;
        }
        .stat-card:hover { transform:translateY(-4px) scale(1.02);box-shadow:0 12px 40px rgba(99,102,241,0.22); }
        @keyframes cardUp { to { opacity:1;transform:translateY(0); } }
        .stat-icon {
            width:46px;height:46px;border-radius:14px;
            display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;
            transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        .stat-card:hover .stat-icon { transform:scale(1.2) rotate(-8deg); }
        .stat-icon.indigo { background:rgba(99,102,241,0.12); }
        .stat-icon.purple { background:rgba(168,85,247,0.12); }
        .stat-icon.green  { background:rgba(16,185,129,0.12); }
        .stat-value { font-size:22px;font-weight:700;letter-spacing:-0.5px; }
        .stat-label { font-size:12px;color:var(--muted);margin-top:2px;font-weight:500; }

        /* Grid */
        .grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(370px,1fr));gap:20px; }

        /* User Card */
        .user-card {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:24px;padding:24px;
            box-shadow:0 8px 32px var(--shadow);
            opacity:0;transform:translateY(32px) scale(0.97);
            animation:cardUp 0.55s cubic-bezier(0.22,1,0.36,1) both;
            position:relative;overflow:hidden;
            transition:transform 0.35s cubic-bezier(0.22,1,0.36,1),
                        box-shadow 0.35s ease, border-color 0.35s ease;
            cursor:default;
        }
        .user-card::before {
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(99,102,241,0.06),rgba(236,72,153,0.04),transparent);
            opacity:0;transition:opacity 0.4s ease;pointer-events:none;border-radius:24px;
        }
        .user-card::after {
            content:'';position:absolute;inset:-1px;border-radius:25px;
            background:linear-gradient(135deg,rgba(99,102,241,0.5),rgba(168,85,247,0.4),rgba(236,72,153,0.3));
            opacity:0;transition:opacity 0.35s ease;z-index:-1;
        }
        .user-card:hover { transform:translateY(-6px) scale(1.01);box-shadow:0 20px 60px rgba(99,102,241,0.2); }
        .user-card:hover::before { opacity:1; }
        .user-card:hover::after  { opacity:1; }

        .card-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:20px; }
        .user-info   { display:flex;align-items:center;gap:12px; }
        .avatar {
            width:44px;height:44px;border-radius:13px;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            display:flex;align-items:center;justify-content:center;
            font-size:17px;font-weight:800;color:white;
            box-shadow:0 4px 14px rgba(99,102,241,0.35);
            transition:transform 0.4s cubic-bezier(0.34,1.56,0.64,1),box-shadow 0.3s ease;
            flex-shrink:0;
        }
        .user-card:hover .avatar { transform:rotate(-6deg) scale(1.12);box-shadow:0 8px 24px rgba(99,102,241,0.5); }
        .user-name { font-size:15px;font-weight:700;letter-spacing:-0.2px; }
        .user-id   { font-size:11px;color:var(--muted);font-family:'DM Mono',monospace;margin-top:2px; }

        .status-pill {
            font-size:11px;font-weight:700;padding:5px 12px;border-radius:100px;
            transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        .user-card:hover .status-pill { transform:scale(1.08); }
        .status-pill.done  { background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.25); }
        .status-pill.going { background:rgba(99,102,241,0.10);color:var(--accent);border:1px solid rgba(99,102,241,0.25); }
        .status-pill.low   { background:rgba(239,68,68,0.10);color:var(--red);border:1px solid rgba(239,68,68,0.25); }

        .progress-section { margin-bottom:18px; }
        .progress-top { display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:12px; }
        .progress-amount {
            font-size:30px;font-weight:800;letter-spacing:-1.5px;
            color: var(--text); /* Tambahkan ini agar warna teks terlihat jelas */
            transition:color 0.3s ease;
        }
        .user-card:hover .progress-amount { color:var(--accent); }
        .progress-amount sup { font-size:13px;color:var(--muted);font-weight:500;margin-left:2px;letter-spacing:0; }
        .progress-right  { text-align:right; }
        .progress-pct    { font-size:14px;font-weight:700;color:var(--accent); }
        .progress-target { font-size:11px;color:var(--muted);font-family:'DM Mono',monospace;margin-top:2px; }

        .bar-track { width:100%;height:8px;background:rgba(99,102,241,0.08);border-radius:100px;overflow:hidden; }
        .bar-fill {
            height:100%;border-radius:100px;
            background:linear-gradient(90deg,var(--accent),var(--accent2));
            transition:width 0.7s cubic-bezier(0.4,0,0.2,1);
            position:relative;overflow:hidden;
        }
        .bar-fill::after {
            content:'';position:absolute;top:0;left:-100%;width:60%;height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,0.5),transparent);
            animation:shimmer 2.5s ease-in-out infinite;
        }
        @keyframes shimmer { to { left:200%; } }
        .bar-fill.done { background:linear-gradient(90deg,#10b981,#34d399); }
        .bar-fill.low  { background:linear-gradient(90deg,#f43f5e,#fb7185); }

        .chart-tabs { display:flex;gap:6px;margin-bottom:10px; }
        .chart-tab {
            font-size:11px;font-weight:600;padding:4px 12px;border-radius:100px;border:none;
            background:rgba(99,102,241,0.08);color:var(--muted);cursor:pointer;
            transition:background 0.2s,color 0.2s,transform 0.2s;font-family:'DM Sans',sans-serif;
        }
        .chart-tab:hover { transform:scale(1.05); }
        .chart-tab.active { background:var(--accent);color:white; }

        .chart-wrap {
            background:rgba(99,102,241,0.04);border:1px solid rgba(99,102,241,0.08);
            border-radius:14px;padding:14px;height:155px;
            transition:background 0.3s ease,border-color 0.3s ease;
        }
        .user-card:hover .chart-wrap { background:rgba(99,102,241,0.07);border-color:rgba(99,102,241,0.18); }

        .reset-info {
            display:flex;align-items:center;gap:6px;
            margin-top:10px;padding:8px 12px;
            background:rgba(99,102,241,0.05);border-radius:10px;
            font-size:11px;color:var(--muted);font-weight:500;
        }
        .reset-info span { color:var(--accent);font-weight:600; }

        ::-webkit-scrollbar { width:5px; }
        ::-webkit-scrollbar-thumb { background:rgba(99,102,241,0.2);border-radius:100px; }
    </style>
</head>
<body>

<div class="orb orb1"></div>
<div class="orb orb2"></div>
<div class="orb orb3"></div>

@include('admin.partials.navbar')

<div class="wrap">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <div class="page-title">📊 Dashboard Monitoring</div>
            <div class="page-sub">Konsumsi air pengguna secara real-time</div>
        </div>
        <div class="header-right">
            <div class="live-badge"><div class="live-dot"></div>LIVE · update tiap 3 detik</div>
        </div>
    </div>

<!-- Ganti bagian blok PHP di bagian atas halaman Blade Anda dengan kode berikut -->
    @php
        use Carbon\Carbon;
        $todayWIB   = Carbon::now('Asia/Jakarta')->toDateString();
        $totalUsers = $users->count();
        
        // Menggunakan properti 'date' langsung dari relasi waterLogs
        $totalMl    = $users->sum(fn($u) =>
            $u->waterLogs->where('date', $todayWIB)->sum('amount')
        );
        $doneCount  = $users->filter(fn($u) =>
            $u->waterLogs->where('date', $todayWIB)->sum('amount') >= ($u->daily_target ?? 2000)
        )->count();
    @endphp

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-card" style="animation-delay:.2s">
            <div class="stat-icon indigo">👥</div>
            <div><div class="stat-value">{{ $totalUsers }}</div><div class="stat-label">Total Pengguna</div></div>
        </div>
        <div class="stat-card" style="animation-delay:.3s">
            <div class="stat-icon purple">💧</div>
            <div><div class="stat-value">{{ number_format($totalMl) }} ml</div><div class="stat-label">Total Konsumsi Hari Ini</div></div>
        </div>
        <div class="stat-card" style="animation-delay:.4s">
            <div class="stat-icon green">✅</div>
            <div><div class="stat-value">{{ $doneCount }} / {{ $totalUsers }}</div><div class="stat-label">Target Tercapai Hari Ini</div></div>
        </div>
    </div>

    <!-- User Grid -->
    <div class="grid" id="user-grid">
        @foreach($users as $i => $user)
        @php
            // Filter menggunakan field 'date' secara langsung untuk hari ini
            $todayTotal = $user->waterLogs->where('date', $todayWIB)->sum('amount');

            $target    = $user->daily_target ?? 2000;
            $pct       = $target > 0 ? min(round(($todayTotal / $target) * 100), 100) : 0;
            $barClass  = $pct >= 100 ? 'done' : ($pct < 30 ? 'low' : '');
            $pillClass = $pct >= 100 ? 'done' : ($pct < 30 ? 'low' : 'going');
            $pillLabel = $pct >= 100 ? 'Tercapai' : ($pct < 30 ? 'Rendah' : 'Berjalan');
            $initial   = strtoupper(substr($user->name, 0, 1));

            $days7  = collect(range(6,0))->map(fn($d) => Carbon::now('Asia/Jakarta')->subDays($d));
            $days30 = collect(range(29,0))->map(fn($d) => Carbon::now('Asia/Jakarta')->subDays($d));

            // Optimasi pembuatan data chart menggunakan relasi waterLogs dan field date
            $makeChart = function($days) use ($user) {
                return [
                    'labels' => $days->map(fn($d) => $d->translatedFormat('d M'))->values(),
                    'data'   => $days->map(fn($day) =>
                        $user->waterLogs->where('date', $day->toDateString())->sum('amount')
                    )->values(),
                ];
            };

            $chart7  = $makeChart($days7);
            $chart30 = $makeChart($days30);

            $midnightWIB = Carbon::now('Asia/Jakarta')->endOfDay();
            $diffHours   = (int) Carbon::now('Asia/Jakarta')->diffInHours($midnightWIB);
            $diffMins    = (int) Carbon::now('Asia/Jakarta')->diff($midnightWIB)->i;
        @endphp

        <div class="user-card" id="user-card-{{ $user->id }}" style="animation-delay:{{ 0.35 + $i * 0.08 }}s">
            <div class="card-header">
                <div class="user-info">
                    <div class="avatar">{{ $initial }}</div>
                    <div>
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-id">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
                <div class="status-pill {{ $pillClass }}">{{ $pillLabel }}</div>
            </div>

            <div class="progress-section">
                <div class="progress-top">
                    <div class="progress-amount total-ml">{{ $todayTotal }}<sup>ml</sup></div>
                    <div class="progress-right">
                        <div class="progress-pct pct-label">{{ $pct }}%</div>
                        <div class="progress-target">target {{ number_format($target) }} ml</div>
                    </div>
                </div>
                <div class="bar-track">
                    <div class="bar-fill {{ $barClass }} bar-fill-el" style="width:{{ $pct }}%"></div>
                </div>
            </div>

            <div class="reset-info">
                🔄 Data reset dalam <span>{{ $diffHours }}j {{ $diffMins }}m</span> &nbsp;·&nbsp; Data lama tetap tersimpan
            </div>

            <div class="chart-tabs" style="margin-top:14px;">
                <button class="chart-tab active" onclick="switchTab(this, {{ $user->id }}, '7d')">7 Hari</button>
                <button class="chart-tab"        onclick="switchTab(this, {{ $user->id }}, '30d')">30 Hari</button>
            </div>
            <div class="chart-wrap"><canvas id="chart-{{ $user->id }}"></canvas></div>
        </div>
        <!-- ... sisa tag script chart per user ... -->

        <script>
        (function(){
            const datasets = {
                '7d':  { labels: @json($chart7['labels']),  data: @json($chart7['data'])  },
                '30d': { labels: @json($chart30['labels']), data: @json($chart30['data']) }
            };
            const userId = {{ $user->id }};

            function buildColors(labels, data) {
                const today = labels[labels.length - 1];
                return {
                    bg:    labels.map((l,i) => l === today ? 'rgba(99,102,241,0.82)' : data[i] > 0 ? 'rgba(168,85,247,0.25)' : 'rgba(99,102,241,0.08)'),
                    hover: labels.map(l    => l === today ? 'rgba(168,85,247,0.95)'  : 'rgba(99,102,241,0.45)'),
                };
            }

            const initData   = datasets['7d'];
            const initColors = buildColors(initData.labels, initData.data);

            const chart = new Chart(document.getElementById('chart-' + userId), {
                type: 'bar',
                data: {
                    labels: initData.labels,
                    datasets: [{ data: initData.data, backgroundColor: initColors.bg, hoverBackgroundColor: initColors.hover, borderRadius: 7, borderSkipped: false }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: 600, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255,255,255,0.95)', titleColor: '#6366f1', bodyColor: '#374151',
                            borderColor: 'rgba(99,102,241,0.2)', borderWidth: 1, padding: 10,
                            callbacks: { label: ctx => ` ${ctx.parsed.y} ml` }
                        }
                    },
                    scales: {
                        x: { ticks: { color: '#9ca3af', font: { size: 10, family: 'DM Mono' }, maxRotation: 0 }, grid: { display: false }, border: { display: false } },
                        y: { ticks: { color: '#9ca3af', font: { size: 10 }, maxTicksLimit: 4 }, grid: { color: 'rgba(99,102,241,0.06)' }, border: { display: false } }
                    }
                }
            });

            window['chartInstance_' + userId] = chart;
            window['chartDatasets_' + userId] = datasets;
            window['buildColors_'   + userId] = buildColors;
        })();
        </script>
        @endforeach
    </div>
</div>

<script>
    function switchTab(btn, userId, range) {
        btn.closest('.chart-tabs').querySelectorAll('.chart-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const chart  = window['chartInstance_' + userId];
        const ds     = window['chartDatasets_' + userId];
        const build  = window['buildColors_'   + userId];
        const d      = ds[range];
        const colors = build(d.labels, d.data);
        chart.data.labels = d.labels;
        chart.data.datasets[0].data = d.data;
        chart.data.datasets[0].backgroundColor = colors.bg;
        chart.data.datasets[0].hoverBackgroundColor = colors.hover;
        chart.update();
    }

// Live update tiap 3 detik
    setInterval(async () => {
        try {
            const data = await (await fetch('/admin/live-data')).json();
            data.forEach(user => {
                const card = document.querySelector(`#user-card-${user.id}`);
                if (!card) return;
                
                // Mencegah pembagian dengan angka nol atau null
                const target = user.target || 2000;
                const pct = target > 0 ? Math.min(Math.round((user.total / target) * 100), 100) : 0;
                
                card.querySelector('.total-ml').innerHTML = `${user.total}<sup>ml</sup>`;
                card.querySelector('.pct-label').textContent = pct + '%';
                
                const bar = card.querySelector('.bar-fill-el');
                bar.style.width = pct + '%';
                bar.className = 'bar-fill bar-fill-el' + (pct >= 100 ? ' done' : pct < 30 ? ' low' : '');
                
                const pill = card.querySelector('.status-pill');
                pill.className = 'status-pill ' + (pct >= 100 ? 'done' : pct < 30 ? 'low' : 'going');
                pill.textContent = pct >= 100 ? 'Tercapai' : pct < 30 ? 'Rendah' : 'Berjalan';
            });
        } catch(e) { console.warn('Live update error:', e); }
    }, 3000);
</script>
</body>
</html>