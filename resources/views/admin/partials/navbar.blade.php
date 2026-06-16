{{-- resources/views/admin/partials/navbar.blade.php --}}
<nav class="navbar">
    <div class="nav-brand">
        <div class="nav-icon">💧</div>
        <div>
            <div class="nav-title">Water Tracker</div>
            <div class="nav-sub">Admin Panel</div>
        </div>
    </div>
    <div class="nav-links">
        <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <span class="nav-link-icon">📊</span> Dashboard
        </a>
        <a href="/admin/users" class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}">
            <span class="nav-link-icon">👥</span> Users
        </a>
        <a href="/admin/logs" class="nav-link {{ request()->is('admin/logs') ? 'active' : '' }}">
            <span class="nav-link-icon">💧</span> Log Air
        </a>
        <a href="/admin/reports" class="nav-link {{ request()->is('admin/reports') ? 'active' : '' }}">
            <span class="nav-link-icon">📈</span> Laporan
        </a>
    </div>
    <div class="nav-right">
        <div class="live-badge"><div class="live-dot"></div>LIVE</div>
        <div class="wib-time">WIB &nbsp;<span id="clock">--:--:--</span></div>
    </div>
</nav>
<script>
    function updateClock() {
        const wib = new Date(Date.now() + 7 * 3600 * 1000);
        const p = n => String(n).padStart(2, '0');
        const el = document.getElementById('clock');
        if (el) el.textContent = `${p(wib.getUTCHours())}:${p(wib.getUTCMinutes())}:${p(wib.getUTCSeconds())}`;
    }
    updateClock(); setInterval(updateClock, 1000);
</script>