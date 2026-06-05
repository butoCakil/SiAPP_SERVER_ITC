@extends('layouts.app')

@section('title', 'Device')
@section('page_title', 'Device Monitor')

@push('styles')
<style>
:root {
    --card-radius: 14px;
    --shadow-card: 0 4px 16px rgba(0,0,0,0.13), inset 0 2px 6px rgba(0,0,0,0.08);
    --shadow-hover: 0 10px 28px rgba(0,0,0,0.22), inset 0 2px 6px rgba(0,0,0,0.10);
}

.device-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 18px;
}

/* Compact view */
.device-grid.compact-view {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 8px;
}
.device-grid.compact-view .device-card {
    display: none;
}
.device-grid.compact-view .device-card-compact {
    display: flex !important;
}
.device-card-compact {
    display: none;
    flex-direction: column;
    background: #fff;
    border: 2px solid #555;
    border-radius: 10px;
    padding: 8px 10px;
    gap: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    position: relative;
    cursor: pointer;
    transition: box-shadow 0.2s;
}
.device-card-compact:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.18); }
.device-card-compact.is-online  { border-color: #06de72; }
.device-card-compact.is-offline { border-color: #ff3b3b; }

/* Baris 1: No Device + Dot */
.dcc-row1 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.dcc-id {
    font-size: 13px;
    font-weight: 700;
    font-family: 'Fira Code', monospace;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dcc-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.dcc-dot.online  { background: #06de72; box-shadow: 0 0 5px #06de72; }
.dcc-dot.offline { background: #ff3b3b; box-shadow: 0 0 5px #ff3b3b; }

/* RSSI bar tipis */
.dcc-rssi-wrap {
    height: 6px;
    background: #eee;
    border-radius: 4px;
    overflow: hidden;
}
.dcc-rssi-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.4s;
}

/* Baris 2: RAM, Buffer, Timestamp */
.dcc-row2 {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 10px;
    color: #555;
    flex-wrap: wrap;
}
.dcc-badge {
    background: #f0f0f0;
    border-radius: 6px;
    padding: 1px 6px;
    font-size: 10px;
    font-weight: 600;
    color: #333;
}
.dcc-badge.buf-ok     { background: #e8f5e9; color: #2e7d32; }
.dcc-badge.buf-warn   { background: #fff8e1; color: #f57f17; }
.dcc-badge.buf-danger { background: #ffebee; color: #c62828; }
.dcc-time { font-size: 9px; color: #999; margin-left: auto; }

.device-card {
    background: #fff;
    border: 3px solid #555;
    border-radius: var(--card-radius);
    box-shadow: var(--shadow-card);
    padding: 14px 16px 12px;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    position: relative;
}

.device-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.device-card.is-online  { border-color: #06de72; }
.device-card.is-offline { border-color: #ff3b3b; }

/* Header */
.dc-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
}

.dc-title { flex: 1; }

.dc-id {
    font-size: 1.1em;
    font-weight: 700;
    font-family: 'Fira Code', monospace;
    display: flex;
    align-items: center;
    gap: 6px;
}

.dc-fw {
    background: #ffd500;
    border-radius: 8px;
    padding: 1px 7px;
    font-size: 10px;
    font-weight: 600;
    box-shadow: inset 0 0 4px rgba(0,0,0,0.2);
}

.dc-info {
    background: #00d0ff;
    border-radius: 8px;
    text-align: center;
    font-size: 11px;
    padding: 1px 8px;
    margin-top: 3px;
    display: inline-block;
    box-shadow: inset 0 0 4px rgba(0,0,0,0.15);
}

.dc-since-online  { font-size: 10px; background: #51f561; color: #000; padding: 1px 8px; border-radius: 8px; margin-top: 3px; display:inline-block; }
.dc-since-offline { font-size: 10px; background: #ee3030; color: #fff; padding: 1px 8px; border-radius: 8px; margin-top: 3px; display:inline-block; }

/* Status dot */
.dc-dot {
    width: 26px; height: 26px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 2px;
}
.dc-dot.online  { background: #06de72; box-shadow: 0 0 14px #00d26a; animation: pulse-online 2s infinite; }
.dc-dot.offline { background: #ff3b3b; box-shadow: 0 0 14px #ff3b3b; }
@keyframes pulse-online {
    0%, 100% { box-shadow: 0 0 8px #00d26a; }
    50%       { box-shadow: 0 0 22px #00ff88, 0 0 40px #00d26a55; }
}
.dc-status-label { font-size: 10px; text-align: center; margin-top: 3px; font-family: monospace; }

/* Bars */
.bar-label {
    font-size: 11px;
    color: #444;
    margin: 7px 0 2px;
}
.dc-buf-unknown { color: #888; font-size:11px; }
.dc-buf-empty   { display:inline-block; background:#e8f5e9; color:#2e7d32; font-weight:700; font-size:11px; padding:1px 8px; border-radius:20px; }
.dc-buf-warn    { display:inline-block; background:#fff3e0; color:#e65100; font-weight:700; font-size:11px; padding:1px 8px; border-radius:20px; }
.dc-buf-danger  { display:inline-block; background:#ffebee; color:#c62828; font-weight:700; font-size:11px; padding:1px 8px; border-radius:20px; }
.ram-bar {
    width: 100%; height: 9px;
    border-radius: 8px;
    overflow: hidden;
    background: linear-gradient(to right, #00aaff, #00ff66 50%, #ffee00 70%, #ff8800 85%, #ff0000);
    position: relative;
}
.ram-bar::after {
    content: "";
    position: absolute;
    top: 0; right: 0;
    height: 100%;
    width: calc(100% - var(--ram-pct, 0%));
    background: #e0e0e0;
    transition: width 0.5s ease;
}

.rssi-bar { width: 100%; height: 9px; border-radius: 8px; background: #e0e0e0; overflow: hidden; position: relative; }
.rssi-fill { position: absolute; top: 0; left: 0; height: 100%; border-radius: 8px; transition: width 0.5s ease, background-color 0.5s ease; }

/* Detail info */
.dc-detail { font-size: 11px; color: #555; margin-top: 6px; background:#f5f7fa; border-radius:8px; padding:5px 8px; }
.dc-detail div { margin-top: 3px; }

/* Buttons */
.dc-actions-top {
    display: flex;
    gap: 5px;
    margin-top: 8px;
    align-items: center;
}

.dc-section-title {
    font-size: 11px;
    font-weight: 700;
    color: #444;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-xs-round {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: inset 0 2px 4px rgba(255,255,255,0.4), inset 0 -2px 4px rgba(0,0,0,0.2), 0 3px 7px rgba(0,0,0,0.2);
    transition: transform 0.2s, filter 0.2s;
}
.btn-xs-round:hover { transform: scale(1.15); filter: brightness(1.1); }
.btn-xs-round:active { transform: scale(0.95); }
.btn-xs-round:disabled { background: #bbb !important; cursor: not-allowed; opacity: 0.6; }

.bxr-set    { background: linear-gradient(135deg,#00aaff,#007bff); }
.bxr-upload { background: linear-gradient(135deg,#00c853,#00964b); }
.bxr-sync   { background: linear-gradient(135deg,#b200ff,#7b1fa2); }
.bxr-reboot { background: linear-gradient(135deg,#ff4b2b,#ff0000); }
.bxr-serial-on  { background: linear-gradient(135deg,#eaff00,#c4d600); }
.bxr-serial-off { background: linear-gradient(135deg,#000,#333); }
.bxr-toggle { background: none; border: none; color: #007bff; font-size: 13px; cursor: pointer; transition: transform 0.3s; }
.bxr-toggle.rotated { transform: rotate(180deg); }

.dc-btn-group {
    display: flex;
    gap: 7px;
    margin-top: 8px;
}

.dc-btn {
    flex: 1;
    padding: 5px 4px;
    border: none;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    box-shadow: inset 0 2px 5px rgba(255,255,255,0.35), inset 0 -2px 5px rgba(0,0,0,0.2), 0 3px 8px rgba(0,0,0,0.22);
    transition: transform 0.2s, filter 0.2s;
}
.dc-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }
.dc-btn:active { transform: translateY(1px); }
.dc-btn:disabled { background: #aaa !important; cursor: not-allowed; opacity: 0.65; }
.dc-btn.btn-set    { background: linear-gradient(135deg,#00aaff,#007bff); }
.dc-btn.btn-upload { background: linear-gradient(135deg,#00c853,#00964b); }
.dc-btn.btn-sync   { background: linear-gradient(135deg,#b200ff,#7b1fa2); }
.dc-btn.btn-reboot { background: linear-gradient(135deg,#ff4b2b,#ff0000); }
.dc-btn.btn-serial-on  { color:#000; background: linear-gradient(135deg,#eaff00,#c4d600); }
.dc-btn.btn-serial-off { background: linear-gradient(135deg,#000,#333); }

.dc-collapse { display: none; }
.dc-collapse.show { display: block; }

.dc-last-setting {
    font-size: 10px;
    background: #f5f5f5;
    border-radius: 8px;
    padding: 6px 8px;
    margin-top: 6px;
    color: #444;
}

.dc-viewlog { font-size: 11px; margin-top: 6px; }
.dc-viewlog a { color: #555; text-decoration: none; }
.dc-viewlog a:hover { color: #007bff; }

/* Global buttons */
.global-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}

.btn-global {
    padding: 7px 18px;
    border: none;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    color: #fff;
    cursor: pointer;
    box-shadow: inset 0 2px 5px rgba(255,255,255,0.3), 0 4px 10px rgba(0,0,0,0.2);
    transition: transform 0.2s, filter 0.2s;
}
.btn-global:hover { transform: translateY(-2px); filter: brightness(1.12); }
.btn-global.g-set    { background: linear-gradient(135deg,#00aaff,#007bff); }
.btn-global.g-sync   { background: linear-gradient(135deg,#b200ff,#7b1fa2); }
.btn-global.g-upload { background: linear-gradient(135deg,#00c853,#00964b); }
.btn-global.g-reboot { background: linear-gradient(135deg,#ff4b2b,#ff0000); }

/* Summary bar */
.summary-bar {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 18px;
    align-items: center;
}
.summary-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.dot-green { width:10px;height:10px;border-radius:50%;background:#06de72;box-shadow:0 0 6px #06de72; }
.dot-red   { width:10px;height:10px;border-radius:50%;background:#ff3b3b;box-shadow:0 0 6px #ff3b3b; }

/* Loading overlay */
.loading-overlay {
    position: fixed; top:0;left:0;width:100%;height:100%;
    background: rgba(255,255,255,0.6);
    display: none;
    justify-content: center; align-items: center;
    z-index: 9999;
    backdrop-filter: blur(3px);
}
.spinner {
    border: 4px solid rgba(0,0,0,0.1);
    border-left-color: #333;
    border-radius: 50%;
    width: 40px; height: 40px;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* CMD Modal */
#cmd-modal-backdrop { display: none; }
#cmd-modal-backdrop.show { display: flex !important; }

.cmd-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 10px;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #eee;
    font-size: 13px;
}
.cmd-row.state-pending  { border-color: #ffe082; background: #fffde7; }
.cmd-row.state-sending  { border-color: #90caf9; background: #e3f2fd; }
.cmd-row.state-ok       { border-color: #a5d6a7; background: #e8f5e9; }
.cmd-row.state-fail     { border-color: #ef9a9a; background: #ffebee; }
.cmd-row.state-skip     { border-color: #ddd;    background: #f5f5f5; opacity:0.6; }

.cmd-row-id   { font-weight:700; font-family:'Fira Code',monospace; min-width:110px; }
.cmd-row-info { font-size:11px; color:#666; flex:1; }
.cmd-row-icon { font-size:16px; min-width:22px; text-align:center; }
.cmd-row-status { font-size:11px; font-weight:600; min-width:90px; text-align:right; }
</style>
@endpush

@section('content')

<div class="loading-overlay" id="loading-overlay">
    <div class="spinner"></div>
</div>

{{-- Summary --}}
<div class="summary-bar" style="justify-content: space-between;">
    <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
    </div>
    <div class="summary-badge">
        <div class="dot-green"></div>
        Online: <strong id="cnt-online">{{ $onlineCount }}</strong>
    </div>
    <div class="summary-badge">
        <div class="dot-red"></div>
        Offline: <strong id="cnt-offline">{{ $offlineCount }}</strong>
    </div>
    <div class="summary-badge">
        📊 Total: <strong id="cnt-total">{{ $onlineCount + $offlineCount }}</strong>
    </div>
    <div class="summary-badge">
        🔄 Refresh: <strong><span id="countdown">60</span>s</strong>
    </div>
    <div class="summary-badge text-muted" style="font-size:11px;">
        Terakhir: <span id="last-refresh">--:--:--</span>
    </div>
</div>

{{-- Global Actions --}}
<div class="global-actions">
    <a href="{{ route('device.registrasi') }}" class="btn-global" style="background:linear-gradient(135deg,#607d8b,#37474f); color:#fff; text-decoration:none; padding:7px 18px; border-radius:20px; font-weight:600; font-size:13px;">
        <i class="fas fa-list mr-1"></i>Kelola Registrasi
    </a>
    <button class="btn-global g-set"    onclick="sendAll('setSetting')">⚙️ Set All</button>
    <button class="btn-global g-sync"   onclick="sendAll('sync')">🔄 Sync All</button>
    <button class="btn-global g-upload" onclick="sendAll('upload')">📤 Upload All</button>
    <button class="btn-global g-reboot" onclick="confirmAll('reboot')">🔁 Reboot All</button>
    <button class="btn-global" id="btn-toggle-view"
        style="background:linear-gradient(135deg,#455a64,#263238);"
        onclick="toggleView()">⚡ Compact</button>
</div>

{{-- Device Grid --}}
<div class="device-grid" id="device-grid">
    @include('device._cards', ['devices' => $devices, 'regDevices' => $regDevices, 'bufferDaily' => $bufferDaily])
</div>

{{-- Modal Progress Command --}}
<div id="cmd-modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:1050; align-items:center; justify-content:center;">
    <div id="cmd-modal" style="background:#fff; border-radius:16px; width:min(560px,95vw); max-height:80vh; display:flex; flex-direction:column; box-shadow:0 8px 40px rgba(0,0,0,0.25); overflow:hidden;">
        {{-- Header --}}
        <div style="padding:14px 20px; background:#1e1e2e; color:#fff; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <span style="font-size:16px; font-weight:700;" id="cmd-modal-title">⚙️ Mengirim Perintah</span>
                <div style="font-size:11px; opacity:0.7; margin-top:2px;" id="cmd-modal-subtitle">Menunggu feedback device...</div>
            </div>
            <button onclick="closeCmdModal()" style="background:transparent; border:none; color:#fff; font-size:18px; cursor:pointer; opacity:0.7;">✕</button>
        </div>
        {{-- Progress bar --}}
        <div style="height:4px; background:#333;">
            <div id="cmd-modal-bar" style="height:100%; background:#06de72; width:0%; transition:width 0.4s;"></div>
        </div>
        {{-- Summary --}}
        <div style="padding:8px 20px; background:#f8f9fa; border-bottom:1px solid #eee; display:flex; gap:16px; font-size:12px;">
            <span>✅ Berhasil: <strong id="cmd-cnt-ok">0</strong></span>
            <span>⏳ Pending: <strong id="cmd-cnt-pending">0</strong></span>
            <span>❌ Gagal: <strong id="cmd-cnt-fail">0</strong></span>
            <span>⏭️ Skip: <strong id="cmd-cnt-skip">0</strong></span>
            <span style="margin-left:auto; color:#888;">Total: <strong id="cmd-cnt-total">0</strong></span>
        </div>
        {{-- Device list --}}
        <div id="cmd-modal-list" style="overflow-y:auto; flex:1; padding:10px 16px; display:flex; flex-direction:column; gap:6px;">
        </div>
        {{-- Footer --}}
        <div style="padding:10px 20px; border-top:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:11px; color:#888;" id="cmd-modal-footer-status">Memproses...</span>
            <button onclick="closeCmdModal()" id="cmd-modal-close-btn"
                style="background:#1e1e2e; color:#fff; border:none; border-radius:20px; padding:6px 20px; font-size:13px; cursor:pointer;">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const DEVICE_KEY = '{{ env("DEVICE_TOKEN", "") }}';

// ── Toggle compact view ──
let isCompact = false;
function toggleView() {
    isCompact = !isCompact;
    const grid = document.getElementById('device-grid');
    const btn  = document.getElementById('btn-toggle-view');
    if (isCompact) {
        grid.classList.add('compact-view');
        btn.textContent = '🗂️ Normal';
        // Init RSSI fill color untuk compact cards
        document.querySelectorAll('.dcc-rssi-fill').forEach(el => {
            el.style.width = (el.dataset.pct || 0) + '%';
            el.style.backgroundColor = getColor(parseFloat(el.dataset.pct) || 0);
        });
    } else {
        grid.classList.remove('compact-view');
        btn.textContent = '⚡ Compact';
        updateBars();
    }
}

// ── Color interpolation untuk RSSI bar ──
function getColor(pct) {
    const stops = [
        [0,   [255,0,0]],
        [20,  [255,50,0]],
        [40,  [255,150,0]],
        [60,  [255,255,0]],
        [80,  [37,211,102]],
        [100, [0,170,255]],
    ];
    let lo = stops[0], hi = stops[stops.length-1];
    for (let i=0; i<stops.length-1; i++) {
        if (pct >= stops[i][0] && pct <= stops[i+1][0]) { lo=stops[i]; hi=stops[i+1]; break; }
    }
    const t = (pct-lo[0])/(hi[0]-lo[0]);
    const r = Math.round(lo[1][0]+t*(hi[1][0]-lo[1][0]));
    const g = Math.round(lo[1][1]+t*(hi[1][1]-lo[1][1]));
    const b = Math.round(lo[1][2]+t*(hi[1][2]-lo[1][2]));
    return `rgb(${r},${g},${b})`;
}

function updateBars() {
    document.querySelectorAll('.rssi-fill').forEach(el => {
        const pct = parseFloat(el.dataset.pct) || 0;
        el.style.width = pct + '%';
        el.style.backgroundColor = getColor(pct);
    });
}

function updateLastRefresh() {
    const now = new Date();
    document.getElementById('last-refresh').textContent =
        [now.getHours(), now.getMinutes(), now.getSeconds()]
        .map(n => String(n).padStart(2,'0')).join(':');
}

async function editLabel(deviceId, currentLabel) {
    const newLabel = prompt('Edit label device ' + deviceId + ':', currentLabel);
    if (newLabel === null) return; // cancelled
    const res = await fetch('/device/' + deviceId + '/label', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ label: newLabel })
    });
    const json = await res.json();
    if (json.status === 'ok') {
        location.reload();
    } else {
        alert('Gagal update label: ' + (json.message ?? 'Unknown error'));
    }
}

// ── Toggle detail collapse ──
function toggleDetail(id) {
    const el = document.getElementById('dc-collapse-'+id);
    const arrow = document.getElementById('dc-arrow-'+id);
    const smBtns = document.getElementById('dc-smbtns-'+id);
    el.classList.toggle('show');
    arrow.classList.toggle('rotated');
    if (smBtns) smBtns.classList.toggle('d-none');
}

// ── Kirim perintah ke satu device ──
async function sendCmd(deviceId, cmdKey, value=1) {
    const body = { device_id: deviceId, [cmdKey]: value };
    try {
        const res = await fetch('/api/device/perintah', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-Device-Key': DEVICE_KEY },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        return data.status === 'ok';
    } catch { return false; }
}

// ── Wrapper dengan feedback tombol ──
async function handleCmd(btn, deviceId, cmdKey, value=1) {
    // Ambil snapshot timestamp sebelum kirim
    let pollData = {};
    try {
        const res  = await fetch('/api-internal/device-poll-status');
        const data = await res.json();
        data.forEach(d => { pollData[d.device_id] = d; });
    } catch(e) {
        // Fallback ke behavior lama jika poll gagal
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '⏳';
        const ok = await sendCmd(deviceId, cmdKey, value);
        btn.innerHTML = ok ? '✅' : '❌';
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2500);
        return;
    }

    const fbKey  = CMD_FEEDBACK_KEY[cmdKey] ?? 'cmd_ts';
    const poll   = pollData[deviceId] ?? {};
    const infoEl = document.querySelector(`[data-device-id="${deviceId}"] .dc-info`);

    const deviceStates = [{
        device_id : deviceId,
        online    : 1,
        skip      : false,
        state     : 'sending',
        ts_before : poll[fbKey] ?? null,
        fb_key    : fbKey,
        info      : infoEl ? infoEl.textContent.trim() : '',
        deadline  : Date.now() + CMD_TIMEOUT_MS,
    }];

    openCmdModal(cmdKey, deviceStates);

    await sendCmd(deviceId, cmdKey, value);

    deviceStates[0].state = 'pending';
    updateCmdRow(deviceId, 'pending', '⏳ Menunggu feedback...');
    updateCmdSummary(deviceStates);

    // Polling feedback
    cmdModalTimer = setInterval(async () => {
        if (!cmdModalActive) { clearInterval(cmdModalTimer); return; }

        let pollNow = {};
        try {
            const res  = await fetch('/api-internal/device-poll-status');
            const data = await res.json();
            data.forEach(d => { pollNow[d.device_id] = d; });
        } catch(e) { return; }

        const now     = Date.now();
        const d       = deviceStates[0];
        const current = pollNow[d.device_id];
        if (!current) return;

        const tsNow = current[d.fb_key] ?? null;
        if (tsNow && tsNow !== d.ts_before) {
            d.state = 'ok';
            updateCmdRow(d.device_id, 'ok', '✅ Berhasil');
            clearInterval(cmdModalTimer);
        } else if (now > d.deadline) {
            d.state = 'fail';
            updateCmdRow(d.device_id, 'fail', '❌ Timeout');
            clearInterval(cmdModalTimer);
        }

        updateCmdSummary(deviceStates);
    }, CMD_POLL_MS);
}

// ── CMD Modal ──
const CMD_LABELS = {
    setSetting : '⚙️ Set Setting',
    sync       : '🔄 Sync',
    upload     : '📤 Upload',
    reboot     : '🔁 Reboot',
    koneksi    : '🔌 Koneksi',
    ota        : '🛠️ OTA',
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
    document.getElementById('cmd-modal-subtitle').textContent = 'Mengirim perintah ke semua device...';
    document.getElementById('cmd-modal-bar').style.width      = '0%';
    document.getElementById('cmd-cnt-ok').textContent         = '0';
    document.getElementById('cmd-cnt-pending').textContent    = deviceStates.filter(d => !d.skip).length;
    document.getElementById('cmd-cnt-fail').textContent       = '0';
    document.getElementById('cmd-cnt-skip').textContent       = deviceStates.filter(d => d.skip).length;
    document.getElementById('cmd-cnt-total').textContent      = deviceStates.length;
    document.getElementById('cmd-modal-footer-status').textContent = 'Memproses...';

    // Render rows
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
    row.querySelector('.cmd-row-icon').textContent  = icon;
    row.querySelector('#cmd-status-' + deviceId).textContent = statusText;
}

function updateCmdSummary(states) {
    const ok      = states.filter(d => d.state === 'ok').length;
    const fail    = states.filter(d => d.state === 'fail').length;
    const skip    = states.filter(d => d.state === 'skip').length;
    const pending = states.filter(d => d.state === 'sending' || d.state === 'pending').length;
    const total   = states.length;
    const done    = ok + fail + skip;

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

async function sendAll(cmdKey) {
    // Ambil snapshot timestamp sebelum kirim
    let pollData = {};
    try {
        const res  = await fetch('/api-internal/device-poll-status');
        const data = await res.json();
        data.forEach(d => { pollData[d.device_id] = d; });
    } catch(e) {
        alert('Gagal ambil status device: ' + e.message);
        return;
    }

    const fbKey = CMD_FEEDBACK_KEY[cmdKey] ?? 'cmd_ts';

    // Kumpulkan device unik
    const seen = new Set();
    const deviceStates = [];
    Array.from(document.querySelectorAll('[data-device-id]')).forEach(el => {
        const id = el.dataset.deviceId;
        if (seen.has(id)) return;
        seen.add(id);
        const poll   = pollData[id] ?? {};
        const isOnline = (poll.online ?? 0) == 1;
        const infoEl = document.querySelector(`[data-device-id="${id}"] .dc-info`);
        deviceStates.push({
            device_id : id,
            online    : isOnline,
            skip      : !isOnline,
            state     : isOnline ? 'sending' : 'skip',
            ts_before : poll[fbKey] ?? null,
            fb_key    : fbKey,
            info      : infoEl ? infoEl.textContent.trim() : '',
            deadline  : Date.now() + CMD_TIMEOUT_MS,
        });
    });

    openCmdModal(cmdKey, deviceStates);

    // Kirim command paralel ke semua yang online
    const sendPromises = deviceStates
        .filter(d => !d.skip)
        .map(d => sendCmd(d.device_id, cmdKey));
    await Promise.all(sendPromises);

    // Update state ke pending setelah terkirim
    deviceStates.filter(d => !d.skip).forEach(d => {
        d.state = 'pending';
        updateCmdRow(d.device_id, 'pending', '⏳ Menunggu feedback...');
    });
    updateCmdSummary(deviceStates);

    // Polling feedback
    cmdModalTimer = setInterval(async () => {
        if (!cmdModalActive) { clearInterval(cmdModalTimer); return; }

        const stillWaiting = deviceStates.filter(d => d.state === 'pending');
        if (stillWaiting.length === 0) {
            clearInterval(cmdModalTimer);
            updateCmdSummary(deviceStates);
            return;
        }

        let pollNow = {};
        try {
            const res  = await fetch('/api-internal/device-poll-status');
            const data = await res.json();
            data.forEach(d => { pollNow[d.device_id] = d; });
        } catch(e) { return; }

        const now = Date.now();
        stillWaiting.forEach(d => {
            const current = pollNow[d.device_id];
            if (!current) return;

            const tsNow = current[d.fb_key] ?? null;
            if (tsNow && tsNow !== d.ts_before) {
                // Timestamp berubah = feedback diterima
                d.state = 'ok';
                updateCmdRow(d.device_id, 'ok', '✅ Berhasil');
            } else if (now > d.deadline) {
                d.state = 'fail';
                updateCmdRow(d.device_id, 'fail', '❌ Timeout');
            }
        });

        updateCmdSummary(deviceStates);

        if (deviceStates.filter(d => d.state === 'pending').length === 0) {
            clearInterval(cmdModalTimer);
        }
    }, CMD_POLL_MS);
}

async function confirmAll(cmdKey) {
    if (cmdKey === 'reboot') {
        if (!confirm(`Yakin ingin reboot semua device?`)) return;
    }
    await sendAll(cmdKey);
}

// ── Auto refresh ──
let countdown = 60;
const cdEl = document.getElementById('countdown');

async function refreshGrid() {
    document.getElementById('loading-overlay').style.display = 'flex';
    try {
        const res = await fetch('/device/cards', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        document.getElementById('device-grid').innerHTML = data.html;
        if (isCompact) {
            document.getElementById('device-grid').classList.add('compact-view');
            document.querySelectorAll('.dcc-rssi-fill').forEach(el => {
                el.style.width = (el.dataset.pct || 0) + '%';
                el.style.backgroundColor = getColor(parseFloat(el.dataset.pct) || 0);
            });
        }
        document.getElementById('cnt-online').textContent = data.online;
        document.getElementById('cnt-offline').textContent = data.offline;
        document.getElementById('cnt-total').textContent = data.total;
        updateBars();
        updateLastRefresh();
        loadAllSparklines();
    } catch(e) {
        console.error('Refresh gagal:', e);
    } finally {
        document.getElementById('loading-overlay').style.display = 'none';
        countdown = 60;
    }
}

setInterval(() => {
    countdown--;
    if (cdEl) cdEl.textContent = countdown;
    if (countdown <= 0) refreshGrid();
}, 1000);

window.addEventListener('load', () => {
    updateBars();
    updateLastRefresh();
});

async function deleteDevice(deviceId) {
    try {
        const res = await fetch(`/device/${deviceId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")?.content || ""
            }
        });
        const data = await res.json();
        if (data.status === "ok") {
            document.querySelector(`[data-device-id="${deviceId}"]`)?.remove();
        } else {
            alert("Gagal hapus device.");
        }
    } catch(e) {
        alert("Error: " + e.message);
    }
}

// ── Sparkline per device ──
async function loadSparkline(deviceId) {
    try {
        const res  = await fetch('/api-internal/device-metrics/' + deviceId);
        const data = await res.json();
        if (!data || data.length === 0) return;

        const labels = data.map(d => d.recorded_at.substring(11, 16));
        const ram    = data.map(d => d.ram);
        const rssi   = data.map(d => Math.min(100, Math.round(((Math.max(-100, Math.min(-40, d.rssi)) + 100) / 60) * 100)));
        const ping   = data.map(d => Math.min(100, d.ping));

        const canvas = document.getElementById('spark-' + deviceId);
        if (!canvas) return;

        new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'RAM%',
                        data: ram,
                        borderColor: '#2196f3',
                        backgroundColor: 'rgba(33,150,243,0.08)',
                        borderWidth: 1.5,
                        pointRadius: 0,
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'RSSI%',
                        data: rssi,
                        borderColor: '#ff9800',
                        backgroundColor: 'rgba(255,152,0,0.08)',
                        borderWidth: 1.5,
                        pointRadius: 0,
                        tension: 0.3,
                        fill: false,
                    },
                    {
                        label: 'Ping%',
                        data: ping,
                        borderColor: '#4caf50',
                        backgroundColor: 'rgba(76,175,80,0.08)',
                        borderWidth: 1.5,
                        pointRadius: 0,
                        tension: 0.3,
                        fill: false,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        titleFont: { size: 10 },
                        bodyFont: { size: 10 },
                    },
                    datalabels: { display: false }
                },
                scales: {
                    x: {
                        ticks: { font: { size: 8 }, maxTicksLimit: 6 },
                        grid: { display: false }
                    },
                    y: {
                        min: 0, max: 100,
                        ticks: { font: { size: 8 }, maxTicksLimit: 4 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    } catch(e) {
        console.warn('Sparkline error ' + deviceId, e);
    }
}

function loadAllSparklines() {
    document.querySelectorAll('[data-device-id]').forEach(el => {
        loadSparkline(el.dataset.deviceId);
    });
}

// Load semua sparkline saat halaman load
document.addEventListener('DOMContentLoaded', () => {
    loadAllSparklines();
});

</script>
@endpush