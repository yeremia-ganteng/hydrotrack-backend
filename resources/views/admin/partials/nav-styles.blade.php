{{-- resources/views/admin/partials/nav-styles.blade.php --}}
<style>
/* ── Navbar ───────────────────────────────────────────── */
.navbar {
    position: sticky; top: 0; z-index: 100;
    display: flex; align-items: center; gap: 0;
    background: rgba(255,255,255,0.82); backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(99,102,241,0.12);
    padding: 0 32px; height: 64px;
    box-shadow: 0 2px 20px rgba(99,102,241,0.08);
}
.nav-brand {
    display: flex; align-items: center; gap: 10px;
    margin-right: 32px; flex-shrink: 0;
}
.nav-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; box-shadow: 0 4px 12px rgba(99,102,241,0.3);
}
.nav-title { font-size: 14px; font-weight: 800; color: #6366f1; letter-spacing: -0.3px; }
.nav-sub   { font-size: 10px; color: #9ca3af; font-weight: 500; margin-top: 1px; }
.nav-links { display: flex; gap: 4px; flex: 1; }
.nav-link {
    display: flex; align-items: center; gap: 7px;
    padding: 8px 16px; border-radius: 10px;
    font-size: 13px; font-weight: 600; color: #6b7280;
    text-decoration: none;
    transition: background 0.2s, color 0.2s, transform 0.2s;
}
.nav-link:hover { background: rgba(99,102,241,0.08); color: #6366f1; transform: translateY(-1px); }
.nav-link.active { background: rgba(99,102,241,0.12); color: #6366f1; }
.nav-link-icon { font-size: 15px; }
.nav-right { display: flex; align-items: center; gap: 12px; margin-left: auto; flex-shrink: 0; }
.live-badge {
    display: flex; align-items: center; gap: 6px;
    background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25);
    border-radius: 100px; padding: 5px 12px;
    font-size: 11px; font-weight: 600; color: #10b981; font-family: 'DM Mono', monospace;
}
.live-dot {
    width: 6px; height: 6px; border-radius: 50%; background: #10b981;
    animation: pulse 1.8s ease-in-out infinite;
}
@keyframes pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(.7); }
}
.wib-time { font-family: 'DM Mono', monospace; font-size: 11px; color: #9ca3af; }
.wib-time span { color: #a855f7; font-weight: 600; }
</style>