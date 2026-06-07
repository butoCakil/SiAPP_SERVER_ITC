<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SiAPP') | SiAPP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- Navbar --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                        <i class="fas fa-user-circle mr-1"></i>
                        {{ session('admin_nama') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        {{-- Sidebar --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('home') }}" class="brand-link">
                <i class="fas fa-wifi brand-image ml-3 elevation-3"
                    style="font-size:24px; opacity:.8; line-height:1.8;"></i>
                <span class="brand-text font-weight-light">SiAPP</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-white ml-1"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ session('admin_nama') }}</a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview {{ request()->routeIs('device*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('device*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-mobile-alt"></i>
                                <p>Device
                                    <span class="badge badge-pill right mr-3" id="badge-offline"
                                        style="background:#f44336; color:#fff; min-width:20px; display:none;"></span>
                                    <span class="badge badge-pill right mr-3" id="badge-online"
                                        style="background:#00c853; color:#fff; min-width:20px; display:none;"></span>
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('device') }}" class="nav-link {{ request()->routeIs('device') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Daftar Device</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('device.ota.bulk') }}" class="nav-link {{ request()->routeIs('device.ota*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>OTA Firmware</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview {{ request()->routeIs('presensi*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('presensi*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-th"></i>
                                <p>Presensi <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('presensi') }}"
                                        class="nav-link {{ request()->routeIs('presensi') ? 'active' : '' }}">
                                        <i class="{{ request()->routeIs('presensi') ? 'fas fa-dot-circle' : 'far fa-circle' }} nav-icon"></i>
                                        <p>Harian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('presensi.event') }}"
                                        class="nav-link {{ request()->routeIs('presensi.event') ? 'active' : '' }}">
                                        <i class="{{ request()->routeIs('presensi.event') ? 'fas fa-dot-circle' : 'far fa-circle' }} nav-icon"></i>
                                        <p>Pembiasaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('presensi.ijin') }}"
                                        class="nav-link {{ request()->routeIs('presensi.ijin*') ? 'active' : '' }}">
                                        <i class="{{ request()->routeIs('presensi.ijin*') ? 'fas fa-dot-circle' : 'far fa-circle' }} nav-icon"></i>
                                        <p>Izin Keluar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('presensi.rekap') }}"
                                        class="nav-link {{ request()->routeIs('presensi.rekap*') ? 'active' : '' }}">
                                        <i class="{{ request()->routeIs('presensi.rekap*') ? 'fas fa-dot-circle' : 'far fa-circle' }} nav-icon"></i>
                                        <p>Rekap</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('siswa') }}"
                                class="nav-link {{ request()->routeIs('siswa*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Siswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('setting') }}"
                                class="nav-link {{ request()->routeIs('setting*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Setting</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kaldik.index') }}" class="nav-link {{ request()->routeIs('kaldik*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Kaldik</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('log') }}" class="nav-link {{ request()->routeIs('log*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Log</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('apikey') }}" class="nav-link {{ request()->routeIs('apikey*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-key"></i>
                                <p>API Key</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('akun') }}" class="nav-link {{ request()->routeIs('akun*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Akun</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-left w-100">
                                    <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                                    <p class="text-danger">Logout</p>
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Content --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <strong>SiAPP</strong> &mdash; Sistem Informasi Presensi dan Pembiasaan
            <div class="float-right d-none d-sm-inline-block">
                <b>Laravel</b> 11
            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"></script>

    <script>
        function updateBadgeOnline() {
            fetch('/api-internal/device-online')
                .then(r => r.json())
                .then(data => {
                    const elOn  = document.getElementById('badge-online');
                    const elOff = document.getElementById('badge-offline');
                    if (elOn) {
                        elOn.textContent    = data.online;
                        elOn.style.display  = data.online > 0 ? 'inline-block' : 'none';
                    }
                    if (elOff) {
                        elOff.textContent    = data.offline;
                        elOff.style.display  = data.offline > 0 ? 'inline-block' : 'none';
                    }
                })
                .catch(() => {});
        }
        updateBadgeOnline();
        setInterval(updateBadgeOnline, 30000);
        </script>
    {{-- ── CMD Modal (shared) ── --}}
    <div id="cmd-modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:1050; align-items:center; justify-content:center;">
        <div id="cmd-modal" style="background:#fff; border-radius:16px; width:min(560px,95vw); max-height:80vh; display:flex; flex-direction:column; box-shadow:0 8px 40px rgba(0,0,0,0.25); overflow:hidden;">
            <div style="padding:14px 20px; background:#1e1e2e; color:#fff; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <span style="font-size:16px; font-weight:700;" id="cmd-modal-title">⚙️ Mengirim Perintah</span>
                    <div style="font-size:11px; opacity:0.7; margin-top:2px;" id="cmd-modal-subtitle">Menunggu feedback device...</div>
                </div>
                <button onclick="closeCmdModal()" style="background:transparent; border:none; color:#fff; font-size:18px; cursor:pointer; opacity:0.7;">✕</button>
            </div>
            <div style="height:4px; background:#333;">
                <div id="cmd-modal-bar" style="height:100%; background:#06de72; width:0%; transition:width 0.4s;"></div>
            </div>
            <div style="padding:8px 20px; background:#f8f9fa; border-bottom:1px solid #eee; display:flex; gap:16px; font-size:12px;">
                <span>✅ Berhasil: <strong id="cmd-cnt-ok">0</strong></span>
                <span>⏳ Pending: <strong id="cmd-cnt-pending">0</strong></span>
                <span>❌ Gagal: <strong id="cmd-cnt-fail">0</strong></span>
                <span>⏭️ Skip: <strong id="cmd-cnt-skip">0</strong></span>
                <span style="margin-left:auto; color:#888;">Total: <strong id="cmd-cnt-total">0</strong></span>
            </div>
            <div id="cmd-modal-list" style="overflow-y:auto; flex:1; padding:10px 16px; display:flex; flex-direction:column; gap:6px;"></div>
            <div style="padding:10px 20px; border-top:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:11px; color:#888;" id="cmd-modal-footer-status">Memproses...</span>
                <button onclick="closeCmdModal()" style="background:#1e1e2e; color:#fff; border:none; border-radius:20px; padding:6px 20px; font-size:13px; cursor:pointer;">Tutup</button>
            </div>
        </div>
    </div>

    <style>
    #cmd-modal-backdrop.show { display: flex !important; }
    .cmd-row {
        display: flex; align-items: center; gap: 10px;
        padding: 7px 10px; border-radius: 8px;
        background: #f8f9fa; border: 1px solid #eee; font-size: 13px;
    }
    .cmd-row.state-pending  { border-color: #ffe082; background: #fffde7; }
    .cmd-row.state-sending  { border-color: #90caf9; background: #e3f2fd; }
    .cmd-row.state-ok       { border-color: #a5d6a7; background: #e8f5e9; }
    .cmd-row.state-fail     { border-color: #ef9a9a; background: #ffebee; }
    .cmd-row.state-skip     { border-color: #ddd;    background: #f5f5f5; opacity:0.6; }
    .cmd-row-id     { font-weight:700; font-family:'Fira Code',monospace; min-width:110px; }
    .cmd-row-info   { font-size:11px; color:#666; flex:1; }
    .cmd-row-icon   { font-size:16px; min-width:22px; text-align:center; }
    .cmd-row-status { font-size:11px; font-weight:600; min-width:90px; text-align:right; }
    </style>

    <script>
    const CMD_LABELS = {
        setSetting   : '⚙️ Set Setting',
        sync         : '🔄 Sync',
        upload       : '📤 Upload',
        reboot       : '🔁 Reboot',
        koneksi      : '🔌 Koneksi',
        ota          : '🛠️ OTA',
        toggleSerial : '🔍 Toggle Serial',
    };
    const CMD_FEEDBACK_KEY = {
        setSetting : 'set_ts',
        sync       : 'cmd_ts',
        upload     : 'cmd_ts',
        reboot     : 'cmd_ts',
        koneksi    : 'cmd_ts',
        ota        : 'cmd_ts',
    };
    const CMD_TIMEOUT_MS = 30000;
    const CMD_POLL_MS    = 2000;

    let cmdModalActive = false;
    let cmdModalTimer  = null;

    function closeCmdModal() {
        cmdModalActive = false;
        if (cmdModalTimer) clearInterval(cmdModalTimer);
        document.getElementById('cmd-modal-backdrop').classList.remove('show');
    }

    function openCmdModal(cmdKey, deviceStates) {
        cmdModalActive = true;
        const label = CMD_LABELS[cmdKey] ?? cmdKey;
        document.getElementById('cmd-modal-title').textContent    = label + ' — Progress';
        document.getElementById('cmd-modal-subtitle').textContent = 'Mengirim perintah...';
        document.getElementById('cmd-modal-bar').style.width      = '0%';
        document.getElementById('cmd-cnt-ok').textContent         = '0';
        document.getElementById('cmd-cnt-pending').textContent    = deviceStates.filter(d => !d.skip).length;
        document.getElementById('cmd-cnt-fail').textContent       = '0';
        document.getElementById('cmd-cnt-skip').textContent       = deviceStates.filter(d => d.skip).length;
        document.getElementById('cmd-cnt-total').textContent      = deviceStates.length;
        document.getElementById('cmd-modal-footer-status').textContent = 'Memproses...';
        const list = document.getElementById('cmd-modal-list');
        list.innerHTML = '';
        deviceStates.forEach(d => {
            const row = document.createElement('div');
            row.className = 'cmd-row ' + (d.skip ? 'state-skip' : 'state-sending');
            row.id = 'cmd-row-' + d.device_id;
            row.innerHTML = `
                <span class="cmd-row-icon">${d.skip ? '⏭️' : '📡'}</span>
                <span class="cmd-row-id">${d.device_id}</span>
                <span class="cmd-row-info">${d.info ?? ''}</span>
                <span class="cmd-row-status" id="cmd-status-${d.device_id}">
                    ${d.skip ? 'Skip (offline)' : 'Mengirim...'}
                </span>
            `;
            list.appendChild(row);
        });
        document.getElementById('cmd-modal-backdrop').classList.add('show');
    }

    function updateCmdRow(deviceId, state, statusText) {
        const row = document.getElementById('cmd-row-' + deviceId);
        if (!row) return;
        row.className = 'cmd-row state-' + state;
        const icon = { ok:'✅', fail:'❌', pending:'⏳', sending:'📡', skip:'⏭️' }[state] ?? '❓';
        row.querySelector('.cmd-row-icon').textContent = icon;
        row.querySelector('#cmd-status-' + deviceId).textContent = statusText;
    }

    function updateCmdSummary(states) {
        const ok      = states.filter(d => d.state === 'ok').length;
        const fail    = states.filter(d => d.state === 'fail').length;
        const skip    = states.filter(d => d.state === 'skip').length;
        const pending = states.filter(d => d.state === 'sending' || d.state === 'pending').length;
        const done    = ok + fail + skip;
        const total   = states.length;
        document.getElementById('cmd-cnt-ok').textContent      = ok;
        document.getElementById('cmd-cnt-fail').textContent    = fail;
        document.getElementById('cmd-cnt-skip').textContent    = skip;
        document.getElementById('cmd-cnt-pending').textContent = pending;
        document.getElementById('cmd-modal-bar').style.width   = Math.round((done / total) * 100) + '%';
        if (pending === 0) {
            document.getElementById('cmd-modal-footer-status').textContent =
                `Selesai — ${ok} berhasil, ${fail} gagal, ${skip} skip`;
            document.getElementById('cmd-modal-subtitle').textContent = 'Semua device telah diproses.';
        }
    }

    function processPollingResult(d, current, onOk, onTimeout, now) {
        const tsNow    = current[d.fb_key] ?? null;
        const isReboot = d.cmd_key === 'reboot';
        if (isReboot) {
            if (!d.reboot_confirmed_at) {
                if (tsNow && tsNow !== d.ts_before) {
                    d.reboot_confirmed_at = Date.now();
                    updateCmdRow(d.device_id, 'pending', '⏳ Restart menunggu online...');
                }
            } else {
                const confirmedTs = Math.floor(d.reboot_confirmed_at / 1000);
                const lastSeenTs  = current.last_seen
                    ? Math.floor(new Date(current.last_seen).getTime() / 1000) : 0;
                if (current.online == 1 && lastSeenTs > confirmedTs) {
                    onOk('✅ Online kembali');
                    return;
                }
            }
        } else {
            const feedbackOk = tsNow && tsNow !== d.ts_before;
            if (feedbackOk) {
                const _detail = (d.fb_key === 'set_ts') ? current.set_detail : current.cmd_detail;
                const _detailStr = _detail ? (typeof _detail === 'object' ? current.set_detail?.status ?? 'settings_applied' : _detail) : null;
                const detailMsg = _detailStr ? '✅ ' + _detailStr : '✅ Berhasil';
                onOk(detailMsg);
                return;
            }
        }
        if (now > d.deadline) onTimeout();
    }
    </script>

    @stack('scripts')
</body>

</html>
