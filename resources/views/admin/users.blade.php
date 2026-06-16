<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | Water Tracker Admin</title>
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
            margin-bottom:28px;
            opacity:0;transform:translateY(-16px);
            animation:slideDown 0.5s cubic-bezier(0.22,1,0.36,1) 0.1s forwards;
        }
        @keyframes slideDown { to { opacity:1;transform:translateY(0); } }
        .page-title { font-size:22px;font-weight:800;letter-spacing:-0.5px;color:var(--accent); }
        .page-sub   { font-size:13px;color:var(--muted);margin-top:3px; }

        .search-bar {
            display:flex;align-items:center;gap:12px;
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:14px;
            padding:10px 16px;box-shadow:0 4px 16px var(--shadow);
            width:280px;
        }
        .search-bar input {
            border:none;outline:none;background:transparent;
            font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);width:100%;
        }
        .search-bar input::placeholder { color:#9ca3af; }

        .table-card {
            background:var(--surface);backdrop-filter:blur(16px);
            border:1px solid var(--border);border-radius:24px;
            box-shadow:0 8px 32px var(--shadow);overflow:hidden;
            opacity:0;transform:translateY(20px);
            animation:cardUp 0.55s cubic-bezier(0.22,1,0.36,1) 0.2s forwards;
        }
        @keyframes cardUp { to { opacity:1;transform:translateY(0); } }

        table { width:100%;border-collapse:collapse; }
        thead th {
            padding:14px 20px;text-align:left;
            font-size:11px;font-weight:700;color:var(--muted);letter-spacing:0.5px;text-transform:uppercase;
            background:rgba(99,102,241,0.04);border-bottom:1px solid rgba(99,102,241,0.1);
        }
        tbody tr {
            border-bottom:1px solid rgba(99,102,241,0.06);
            transition:background 0.2s;
        }
        tbody tr:last-child { border-bottom:none; }
        tbody tr:hover { background:rgba(99,102,241,0.04); }
        tbody td { padding:14px 20px;font-size:13px;vertical-align:middle; }

        .avatar-sm {
            width:36px;height:36px;border-radius:10px;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            display:flex;align-items:center;justify-content:center;
            font-size:14px;font-weight:800;color:white;flex-shrink:0;
        }
        .user-cell { display:flex;align-items:center;gap:12px; }
        .user-cell-name { font-weight:600;font-size:14px; }
        .user-cell-id   { font-size:11px;color:var(--muted);font-family:'DM Mono',monospace; }

        .pill {
            display:inline-flex;align-items:center;
            font-size:11px;font-weight:700;padding:4px 11px;border-radius:100px;
        }
        .pill-green { background:rgba(16,185,129,0.1);color:#059669;border:1px solid rgba(16,185,129,0.2); }
        .pill-blue  { background:rgba(99,102,241,0.1);color:var(--accent);border:1px solid rgba(99,102,241,0.2); }
        .pill-red   { background:rgba(239,68,68,0.1);color:var(--red);border:1px solid rgba(239,68,68,0.2); }

        .progress-mini { display:flex;align-items:center;gap:8px; }
        .bar-mini { width:80px;height:5px;background:rgba(99,102,241,0.1);border-radius:100px;overflow:hidden; }
        .bar-mini-fill { height:100%;border-radius:100px;background:linear-gradient(90deg,var(--accent),var(--accent2)); }
        .bar-mini-fill.done { background:linear-gradient(90deg,#10b981,#34d399); }
        .bar-mini-fill.low  { background:linear-gradient(90deg,#f43f5e,#fb7185); }

        .btn-action {
            padding:6px 14px;border-radius:8px;border:none;cursor:pointer;
            font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;
            transition:transform 0.2s,opacity 0.2s;text-decoration:none;display:inline-block;
        }
        .btn-action:hover { transform:scale(1.05); }
        .btn-detail { background:rgba(99,102,241,0.1);color:var(--accent); }
        .btn-delete { background:rgba(239,68,68,0.1);color:var(--red); }

        .empty-state {
            text-align:center;padding:60px 20px;color:var(--muted);
        }
        .empty-state .emoji { font-size:48px;margin-bottom:12px; }

        /* Modal */
        .modal-overlay {
            display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);
            backdrop-filter:blur(4px);z-index:200;
            align-items:center;justify-content:center;
        }
        .modal-overlay.show { display:flex; }
        .modal {
            background:white;border-radius:24px;padding:32px;
            max-width:480px;width:90%;box-shadow:0 24px 80px rgba(0,0,0,0.2);
            animation:modalIn 0.3s cubic-bezier(0.22,1,0.36,1);
        }
        @keyframes modalIn { from { opacity:0;transform:scale(0.92) translateY(20px); } }
        .modal-title { font-size:18px;font-weight:800;color:var(--red);margin-bottom:8px; }
        .modal-desc  { font-size:14px;color:var(--muted);line-height:1.6;margin-bottom:24px; }
        .modal-actions { display:flex;gap:10px;justify-content:flex-end; }
        .btn-cancel { background:#f3f4f6;color:#374151;border:none;padding:10px 20px;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif; }
        .btn-confirm-del { background:#ef4444;color:white;border:none;padding:10px 20px;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif; }

        @media(max-width:768px) {
            .wrap { padding:16px; }
            .page-header { flex-direction:column;align-items:flex-start;gap:12px; }
            .search-bar { width:100%; }
            .col-hide { display:none; }
        }
    </style>
</head>
<body>

<div class="orb orb1"></div>
<div class="orb orb2"></div>

@include('admin.partials.navbar')

<div class="wrap">
    <div class="page-header">
        <div>
            <div class="page-title">👥 Manajemen Users</div>
            <div class="page-sub">{{ $users->count() }} pengguna terdaftar</div>
        </div>
        <div class="search-bar">
            <span style="font-size:16px">🔍</span>
            <input type="text" id="searchInput" placeholder="Cari nama atau email..." onkeyup="filterTable()">
        </div>
    </div>

    <div class="table-card">
        <table id="usersTable">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th class="col-hide">Target Harian</th>
                    <th>Konsumsi Hari Ini</th>
                    <th class="col-hide">Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php use Carbon\Carbon; $todayWIB = Carbon::now('Asia/Jakarta')->toDateString(); @endphp
                @forelse($users as $user)
                @php
                    $todayWIB   = Carbon::now('Asia/Jakarta')->toDateString();
                    $todayTotal = $user->waterlogs->filter(fn($l) =>
                        Carbon::parse($l->created_at)->setTimezone('Asia/Jakarta')->toDateString() === $todayWIB
                    )->sum('amount');
                    $target  = $user->daily_target ?? 2000;
                    $pct     = min(round(($todayTotal / $target) * 100), 100);
                    $barCls  = $pct >= 100 ? 'done' : ($pct < 30 ? 'low' : '');
                    $pillCls = $pct >= 100 ? 'pill-green' : ($pct < 30 ? 'pill-red' : 'pill-blue');
                    $pillLbl = $pct >= 100 ? 'Tercapai' : ($pct < 30 ? 'Rendah' : 'Berjalan');
                @endphp
                <tr data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                    <td>
                        <div class="user-cell">
                            <div class="avatar-sm">{{ strtoupper(substr($user->name,0,1)) }}</div>
                            <div>
                                <div class="user-cell-name">{{ $user->name }}</div>
                                <div class="user-cell-id">#{{ str_pad($user->id,4,'0',STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--muted);font-family:'DM Mono',monospace;font-size:12px;">{{ $user->email }}</td>
                    <td class="col-hide">
                        <span style="font-weight:600;">{{ number_format($target) }} ml</span>
                    </td>
                    <td>
                        <div class="progress-mini">
                            <div class="bar-mini"><div class="bar-mini-fill {{ $barCls }}" style="width:{{ $pct }}%"></div></div>
                            <span class="pill {{ $pillCls }}">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td class="col-hide" style="color:var(--muted);font-size:12px;">
                        {{ Carbon::parse($user->created_at)->setTimezone('Asia/Jakarta')->format('d M Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="/admin/user-history/{{ $user->id }}" class="btn-action btn-detail">Detail</a>
                            <button class="btn-action btn-delete" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><div class="emoji">👤</div><div>Belum ada pengguna terdaftar</div></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <div class="modal-title">🗑️ Hapus Pengguna</div>
        <div class="modal-desc" id="deleteModalDesc">Apakah kamu yakin ingin menghapus pengguna ini? Semua data log air mereka juga akan ikut terhapus dan tidak bisa dikembalikan.</div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn-confirm-del">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#usersTable tbody tr[data-name]').forEach(row => {
            const match = row.dataset.name.includes(q) || row.dataset.email.includes(q);
            row.style.display = match ? '' : 'none';
        });
    }

    function confirmDelete(id, name) {
        document.getElementById('deleteModalDesc').textContent =
            `Apakah kamu yakin ingin menghapus "${name}"? Semua data log air mereka juga akan terhapus.`;
        document.getElementById('deleteForm').action = `/admin/users/${id}`;
        document.getElementById('deleteModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
</body>
</html>