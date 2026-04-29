<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiAPP — Sistem Informasi Absensi Presensi Pintar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --bg: #0a0e1a;
        --surface: #111827;
        --card: #1a2235;
        --border: rgba(255,255,255,0.07);
        --text: #e2e8f0;
        --muted: #64748b;
        --accent: #3b82f6;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: var(--bg);
        color: var(--text);
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Background animated */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse at 20% 20%, rgba(59,130,246,0.08) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 80%, rgba(139,92,246,0.06) 0%, transparent 60%);
        pointer-events: none;
        z-index: 0;
    }

    /* ── HEADER ── */
    header {
        position: relative;
        z-index: 10;
        background: rgba(17,24,39,0.95);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--border);
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .brand img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(59,130,246,0.4);
    }

    .brand-text h1 {
        font-size: 20px;
        font-weight: 800;
        background: linear-gradient(135deg, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: 2px;
    }

    .brand-text p {
        font-size: 10px;
        color: var(--muted);
        letter-spacing: 0.5px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .jam-header {
        font-family: 'Courier New', monospace;
        font-size: 22px;
        font-weight: 700;
        color: #60a5fa;
        letter-spacing: 2px;
    }

    .btn-login {
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
    }
    .btn-login:hover { transform: translateY(-1px); filter: brightness(1.1); color:#fff; text-decoration:none; }

    /* ── MAIN ── */
    main {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 20px;
    }

    /* ── STATUS CARDS ── */
    .status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 24px;
    }

    @media (max-width: 640px) { .status-grid { grid-template-columns: 1fr; } }

    .status-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .status-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px;
        height: 100%;
        border-radius: 4px 0 0 4px;
    }

    .status-card:hover { transform: translateY(-2px); }

    .status-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .status-info { flex: 1; }
    .status-type { font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; color: var(--muted); margin-bottom: 4px; }
    .status-label { font-size: 22px; font-weight: 800; letter-spacing: 1px; }
    .status-sub { font-size: 11px; color: var(--muted); margin-top: 3px; }

    .pulse-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        animation: pulse 2s infinite;
        flex-shrink: 0;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }

    /* ── STAT MINI ── */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 24px;
    }

    .stat-mini {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        text-align: center;
    }

    .stat-mini .val { font-size: 28px; font-weight: 800; }
    .stat-mini .lbl { font-size: 11px; color: var(--muted); margin-top: 2px; }

    /* ── FILTER & TABS ── */
    .toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .tab-group {
        display: flex;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
    }

    .tab-btn {
        padding: 8px 18px;
        border: none;
        background: transparent;
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .tab-btn.active { background: var(--accent); color: #fff; }
    .tab-btn:hover:not(.active) { color: var(--text); }

    .kelas-select {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 8px 14px;
        color: var(--text);
        font-size: 12px;
        outline: none;
        cursor: pointer;
    }

    .search-box {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 8px 14px;
        color: var(--text);
        font-size: 12px;
        outline: none;
        flex: 1;
        min-width: 160px;
    }
    .search-box::placeholder { color: var(--muted); }

    /* ── TABLE ── */
    .data-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    .data-card table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-card thead tr {
        background: rgba(255,255,255,0.03);
        border-bottom: 1px solid var(--border);
    }

    .data-card th {
        padding: 10px 14px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted);
        text-align: left;
        font-weight: 600;
    }

    .data-card td {
        padding: 10px 14px;
        font-size: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.03);
    }

    .data-card tr:last-child td { border-bottom: none; }
    .data-card tr:hover td { background: rgba(255,255,255,0.02); }

    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .badge-tw  { background: rgba(0,200,83,0.15);  color: #00c853; }
    .badge-tl  { background: rgba(255,136,0,0.15); color: #ff8800; }
    .badge-tlt { background: rgba(244,67,54,0.15); color: #f44336; }
    .badge-plg { background: rgba(59,130,246,0.15);color: #3b82f6; }
    .badge-d   { background: rgba(255,136,0,0.15); color: #ff8800; }
    .badge-a   { background: rgba(156,39,176,0.15);color: #9c27b0; }
    .badge-i   { background: rgba(233,30,140,0.15);color: #e91e8c; }
    .badge-keduanya { background: rgba(0,200,83,0.15); color:#00c853; }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: var(--muted);
    }
    .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.3; }

    /* refresh indicator */
    .refresh-bar {
        height: 2px;
        background: linear-gradient(90deg, #3b82f6, #a78bfa);
        width: 0%;
        transition: width linear;
        position: fixed;
        top: 0; left: 0; z-index: 999;
    }

    .nama-text { font-weight: 600; }
    .kelas-text { font-size: 10px; color: var(--muted); }

    .table-wrapper {
        max-height: 480px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.15) transparent;
    }
    .table-wrapper::-webkit-scrollbar { width: 6px; }
    .table-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
        border-radius: 3px;
    }
    .data-card thead th {
        position: sticky;
        top: 0;
        background: #1a2235;
        z-index: 2;
    }
    </style>
</head>
<body>

<div class="refresh-bar" id="refresh-bar"></div>

{{-- Header --}}
<header>
    <div class="brand">
        <img src="/img/logoInstitusi.png" alt="Logo">
        <div class="brand-text">
            <h1>SiAPP</h1>
            <p>Sistem Informasi Absensi Presensi Pintar</p>
        </div>
    </div>
    <div class="header-right">
        <div class="jam-header" id="jam-live">{{ date('H:i:s') }}</div>
        <a href="{{ route('login') }}" class="btn-login">
            <i class="fas fa-lock mr-1"></i>&nbsp;Admin
        </a>
    </div>
</header>

<main>

    {{-- Status Cards --}}
    <div class="status-grid">

        {{-- Presensi Masuk/Pulang --}}
        <div class="status-card" style="border-color: {{ $statusMasuk['color'] }}22;">
            <div style="position:absolute; top:0; left:0; width:4px; height:100%; background:{{ $statusMasuk['color'] }}; border-radius:4px 0 0 4px;"></div>
            <div class="status-icon" style="background: {{ $statusMasuk['color'] }}18;">
                <i class="fas fa-{{ $statusMasuk['icon'] }}" style="color: {{ $statusMasuk['color'] }};"></i>
            </div>
            <div class="status-info">
                <div class="status-type">Presensi Masuk / Pulang</div>
                <div class="status-label" style="color: {{ $statusMasuk['color'] }};">{{ $statusMasuk['label'] }}</div>
                <div class="status-sub">{{ $statusMasuk['sub'] }}</div>
                @if($setting)
                <div class="status-sub mt-1">
                    🕐 Masuk: {{ $setting->wa }} – {{ $setting->wta }}
                    &nbsp;|&nbsp;
                    🕓 Pulang: {{ $setting->wtp }} – {{ $setting->wp }}
                </div>
                @endif
            </div>
            <div class="pulse-dot" style="background: {{ $statusMasuk['color'] }};"></div>
        </div>

        {{-- Presensi Sholat --}}
        <div class="status-card" style="border-color: {{ $statusSholat['color'] }}22;">
            <div style="position:absolute; top:0; left:0; width:4px; height:100%; background:{{ $statusSholat['color'] }}; border-radius:4px 0 0 4px;"></div>
            <div class="status-icon" style="background: {{ $statusSholat['color'] }}18;">
                <i class="fas fa-mosque" style="color: {{ $statusSholat['color'] }};"></i>
            </div>
            <div class="status-info">
                <div class="status-type">Presensi Sholat</div>
                <div class="status-label" style="color: {{ $statusSholat['color'] }};">
                    {{ $statusSholat['aktif'] ? $statusSholat['label'].' AKTIF' : 'TUTUP' }}
                </div>
                <div class="status-sub">
                    🕛 Dzuhur: 11:45 – 14:30
                    &nbsp;|&nbsp;
                    🕓 Ashar: 14:30 – 17:00
                </div>
            </div>
            @if($statusSholat['aktif'])
            <div class="pulse-dot" style="background: {{ $statusSholat['color'] }};"></div>
            @endif
        </div>

    </div>

    {{-- Stat Mini --}}
    <div class="stat-row">
        <div class="stat-mini">
            <div class="val" style="color:#00c853;">{{ $totalHadir }}</div>
            <div class="lbl">Hadir Hari Ini</div>
        </div>
        <div class="stat-mini">
            <div class="val" style="color:#ff8800;">{{ $totalDzuhur }}</div>
            <div class="lbl">Sholat Dzuhur</div>
        </div>
        <div class="stat-mini">
            <div class="val" style="color:#9c27b0;">{{ $totalAshar }}</div>
            <div class="lbl">Sholat Ashar</div>
        </div>
        <div class="stat-mini">
            <div class="val" style="color:#e91e8c;">{{ $totalIzin }}</div>
            <div class="lbl">🌸 Izin Mens</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="tab-group">
            <a href="{{ route('home', ['tab'=>'presensi', 'kelas'=>$filterKelas]) }}"
                class="tab-btn {{ $tab==='presensi' ? 'active' : '' }}">
                <i class="fas fa-user-check"></i> Presensi
            </a>
            <a href="{{ route('home', ['tab'=>'sholat', 'kelas'=>$filterKelas]) }}"
                class="tab-btn {{ $tab==='sholat' ? 'active' : '' }}">
                <i class="fas fa-mosque"></i> Sholat
            </a>
        </div>
        <form action="{{ route('home') }}" method="GET" style="display:contents;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="kelas" class="kelas-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k }}" {{ $filterKelas===$k ? 'selected':'' }}>{{ $k }}</option>
                @endforeach
            </select>
        </form>
        <input type="text" class="search-box" id="search-input"
            placeholder="🔍 Cari nama...">
        <span style="font-size:11px; color:var(--muted); margin-left:auto;">
            📅 {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
        </span>
    </div>

    {{-- Data Table --}}
    <div class="data-card">
        <div class="table-wrapper">
            @if($tab === 'presensi')
            <table id="main-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Masuk</th>
                        <th>Ket</th>
                        <th>Pulang</th>
                        <th>Ket</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPresensi as $i => $p)
                    <tr data-search="{{ strtolower($p->nama) }}">
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="nama-text">{{ $p->nama }}</div>
                            <div class="kelas-text">{{ $p->nomorinduk }}</div>
                        </td>
                        <td>{{ $p->info }}</td>
                        <td>{{ $p->waktumasuk ?? '-' }}</td>
                        <td>
                            @if(in_array($p->ketmasuk, ['M','TW','MSK']))
                                <span class="badge badge-tw">Tepat</span>
                            @elseif(in_array($p->ketmasuk, ['T','TL','TLT']))
                                <span class="badge badge-tl">Toleransi</span>
                            @elseif($p->ketmasuk === 'TLT')
                                <span class="badge badge-tlt">Terlambat</span>
                            @else
                                <span class="badge" style="background:rgba(255,255,255,0.05);color:var(--muted);">{{ $p->ketmasuk ?? '-' }}</span>
                            @endif
                        </td>
                        <td>{{ ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : '-' }}</td>
                        <td>
                            @if(in_array($p->ketpulang, ['P','PLG']))
                                <span class="badge badge-plg">Normal</span>
                            @elseif($p->ketpulang === 'PA')
                                <span class="badge badge-tl">Awal</span>
                            @else
                                <span class="badge" style="background:rgba(255,255,255,0.05);color:var(--muted);">{{ $p->ketpulang ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                Belum ada data presensi hari ini
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @else
            <table id="main-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Dzuhur</th>
                        <th>Ashar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sholatList as $i => $s)
                    <tr data-search="{{ strtolower($s['nama']) }}">
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="nama-text">{{ $s['nama'] }}</div>
                        </td>
                        <td>{{ $s['kelas'] }}</td>
                        <td>
                            @if($s['dzuhur'])
                                @if($s['izin_mens'])
                                    <span class="badge badge-i">🌸 {{ $s['dzuhur'] }}</span>
                                @else
                                    <span class="badge badge-d">🕛 {{ $s['dzuhur'] }}</span>
                                @endif
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($s['ashar'])
                                <span class="badge badge-a">🕓 {{ $s['ashar'] }}</span>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($s['izin_mens'])
                                <span class="badge badge-i">🌸 Izin Mens</span>
                            @elseif($s['dzuhur'] && $s['ashar'])
                                <span class="badge badge-keduanya">✅ Lengkap</span>
                            @elseif($s['dzuhur'] || $s['ashar'])
                                <span class="badge badge-tl">⚠️ Sebagian</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-mosque"></i>
                                Belum ada data sholat hari ini
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif
        </div>
    </div>

</main>

<script>
// ── Jam realtime ──
function updateJam() {
    const now = new Date();
    const t = [now.getHours(), now.getMinutes(), now.getSeconds()]
        .map(n => String(n).padStart(2,'0')).join(':');
    document.getElementById('jam-live').textContent = t;
}
setInterval(updateJam, 1000);

// ── Search ──
document.getElementById('search-input').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#main-table tbody tr[data-search]').forEach(row => {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
    });
});

// ── Auto refresh 30 detik ──
let secs = 30;
const bar = document.getElementById('refresh-bar');
const timer = setInterval(() => {
    secs--;
    bar.style.width = ((30 - secs) / 30 * 100) + '%';
    bar.style.transitionDuration = '1s';
    if (secs <= 0) {
        clearInterval(timer);
        location.reload();
    }
}, 1000);
</script>
</body>
</html>