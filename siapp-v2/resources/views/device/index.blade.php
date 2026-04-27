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
.dc-dot.online  { background: #06de72; box-shadow: 0 0 14px #00d26a; }
.dc-dot.offline { background: #ff3b3b; box-shadow: 0 0 14px #ff3b3b; }
.dc-status-label { font-size: 10px; text-align: center; margin-top: 3px; font-family: monospace; }

/* Bars */
.bar-label {
    font-size: 11px;
    color: #444;
    margin: 7px 0 2px;
}

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
.dc-detail { font-size: 11px; color: #555; margin-top: 6px; }
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
</style>
@endpush

@section('content')

<div class="loading-overlay" id="loading-overlay">
    <div class="spinner"></div>
</div>

{{-- Summary --}}
<div class="summary-bar">
    <div class="summary-badge">
        <div class="dot-green"></div>
        Online: <strong id="cnt-online">{{ $onlineCount }}</strong>
    </div>
    <div class="summary-badge">
        <div class="dot-red"></div>
        Offline: <strong id="cnt-offline">{{ $offlineCount }}</strong>
    </div>
    <div class="summary-badge">
        📊 Total: <strong>{{ $onlineCount + $offlineCount }}</strong>
    </div>
    <div class="summary-badge">
        🔄 Refresh: <strong><span id="countdown">30</span>s</strong>
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
</div>

{{-- Device Grid --}}
<div class="device-grid" id="device-grid">
    @include('device._cards', ['devices' => $devices, 'regDevices' => $regDevices])
</div>

@endsection

@push('scripts')
<script>
const DEVICE_KEY = '{{ env("DEVICE_TOKEN", "") }}';

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

// ── Toggle detail collapse ──
function toggleDetail(id) {
    const el = document.getElementById('dc-collapse-'+id);
    const arrow = document.getElementById('dc-arrow-'+id);
    const smBtns = document.getElementById('dc-smbtns-'+id);
    const delBtn = document.getElementById('dc-del-'+id);
    el.classList.toggle('show');
    arrow.classList.toggle('rotated');
    if (smBtns) smBtns.classList.toggle('d-none');
    if (delBtn) {
        delBtn.style.display = el.classList.contains('show') ? 'inline-flex' : 'none';
    }
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
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '⏳';
    const ok = await sendCmd(deviceId, cmdKey, value);
    btn.innerHTML = ok ? '✅' : '❌';
    setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2500);
}

// ── Kirim ke semua device ──
async function sendAll(cmdKey) {
    document.getElementById('loading-overlay').style.display = 'flex';
    const cards = document.querySelectorAll('[data-device-id]');
    const promises = Array.from(cards).map(el => sendCmd(el.dataset.deviceId, cmdKey));
    const results = await Promise.all(promises);
    document.getElementById('loading-overlay').style.display = 'none';
    const ok = results.filter(Boolean).length;
    alert(`✅ Berhasil: ${ok} / ${results.length} device`);
}

async function confirmAll(cmdKey) {
    if (!confirm(`Yakin ingin ${cmdKey} semua device?`)) return;
    await sendAll(cmdKey);
}

// ── Auto refresh ──
let countdown = 30;
const cdEl = document.getElementById('countdown');

async function refreshGrid() {
    document.getElementById('loading-overlay').style.display = 'flex';
    try {
        const res = await fetch('/device/cards');
        const html = await res.text();
        document.getElementById('device-grid').innerHTML = html;
        updateBars();
        updateLastRefresh();
    } catch(e) {
        console.error('Refresh gagal:', e);
    } finally {
        document.getElementById('loading-overlay').style.display = 'none';
        countdown = 30;
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

</script>
@endpush