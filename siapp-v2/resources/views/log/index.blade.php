@extends('layouts.app')
@section('title', 'Log Management')
@section('page_title', 'Log Management')

@push('styles')
<style>
/* ── Layout Split ── */
.log-wrapper { display:flex; height:calc(100vh - 160px); gap:0; border:1px solid #dee2e6; border-radius:8px; overflow:hidden; }
.log-sidebar { width:240px; min-width:200px; background:#1e2a3a; color:#cdd6e0; overflow-y:auto; flex-shrink:0; }
.log-main    { flex:1; display:flex; flex-direction:column; overflow:hidden; background:#fff; }

/* ── Sidebar ── */
.log-sidebar-header { padding:10px 14px; font-size:11px; font-weight:700; text-transform:uppercase; color:#7a9cb8; border-bottom:1px solid #2d3f52; letter-spacing:1px; }
.log-section-title { padding:8px 14px 4px; font-size:10px; font-weight:700; text-transform:uppercase; color:#4a7fa5; letter-spacing:1px; }
.log-device-group { border-bottom:1px solid #2a3a4a; }
.log-device-header { display:flex; align-items:center; gap:6px; padding:7px 14px; cursor:pointer; font-size:12px; font-weight:600; color:#a8c0d6; transition:background 0.15s; }
.log-device-header:hover { background:#253447; }
.log-device-header.active { background:#2d4a6a; color:#fff; }
.log-device-header i.arrow { margin-left:auto; font-size:10px; transition:transform 0.2s; }
.log-device-header.open i.arrow { transform:rotate(90deg); }
.log-date-list { display:none; }
.log-date-list.open { display:block; }
.log-date-item { padding:5px 14px 5px 32px; font-size:11px; color:#7a9cb8; cursor:pointer; transition:background 0.15s; display:flex; justify-content:space-between; }
.log-date-item:hover { background:#253447; color:#cdd6e0; }
.log-date-item.active { background:#1a6ba0; color:#fff; }
.log-single-item { padding:7px 14px; font-size:12px; color:#a8c0d6; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background 0.15s; border-bottom:1px solid #2a3a4a; }
.log-single-item:hover { background:#253447; }
.log-single-item.active { background:#2d4a6a; color:#fff; }
.log-badge { font-size:9px; background:#2d4a6a; color:#7ab8e0; padding:1px 6px; border-radius:8px; margin-left:auto; }

/* ── Main Panel ── */
.log-tabs { display:flex; border-bottom:1px solid #dee2e6; background:#f8f9fa; flex-shrink:0; }
.log-tab { padding:8px 16px; font-size:12px; font-weight:600; cursor:pointer; color:#6c757d; border-bottom:2px solid transparent; transition:all 0.15s; }
.log-tab:hover { color:#007bff; }
.log-tab.active { color:#007bff; border-bottom-color:#007bff; background:#fff; }
.log-tab-pane { display:none; flex:1; overflow:hidden; flex-direction:column; }
.log-tab-pane.active { display:flex; }

/* ── Log Content ── */
.log-toolbar { padding:8px 12px; border-bottom:1px solid #dee2e6; background:#f8f9fa; display:flex; gap:8px; align-items:center; flex-shrink:0; flex-wrap:wrap; }
.log-content { flex:1; overflow-y:auto; font-family:monospace; font-size:11px; }
.log-table { width:100%; border-collapse:collapse; }
.log-table th { background:#343a40; color:#fff; padding:6px 10px; font-size:11px; position:sticky; top:0; z-index:1; }
.log-table td { padding:5px 10px; border-bottom:1px solid #f0f0f0; font-size:11px; vertical-align:top; }
.log-table tr:hover td { background:#f8f9fa; }
.log-pre { padding:12px; white-space:pre-wrap; word-break:break-all; line-height:1.5; color:#333; background:#fff; }
.log-pre .log-error   { color:#dc3545; }
.log-pre .log-warning { color:#fd7e14; }
.log-pre .log-info    { color:#0d6efd; }
.log-empty { display:flex; align-items:center; justify-content:center; height:100%; color:#aaa; font-size:13px; flex-direction:column; gap:8px; }
.log-loading { display:flex; align-items:center; justify-content:center; height:100%; color:#aaa; font-size:13px; }

/* ── Pagination ── */
.log-pagination { padding:8px 12px; border-top:1px solid #dee2e6; background:#f8f9fa; display:flex; gap:6px; align-items:center; flex-shrink:0; font-size:12px; }

/* ── Delete actions ── */
.log-actions { padding:8px 12px; border-top:1px solid #dee2e6; background:#fff8f8; display:flex; gap:6px; flex-wrap:wrap; flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="log-wrapper">

    {{-- ═══════════════════════════════ --}}
    {{-- PANEL KIRI (SIDEBAR) --}}
    {{-- ═══════════════════════════════ --}}
    <div class="log-sidebar" id="log-sidebar">
        <div class="log-sidebar-header"><i class="fas fa-list mr-1"></i>Log Sources</div>
        <div id="sidebar-content">
            <div style="padding:20px; text-align:center; color:#4a7fa5; font-size:12px;">
                <i class="fas fa-spinner fa-spin"></i> Memuat...
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- PANEL KANAN (MAIN) --}}
    {{-- ═══════════════════════════════ --}}
    <div class="log-main">

        {{-- Tab Headers --}}
        <div class="log-tabs" id="log-tabs" style="display:none;">
            <div class="log-tab active" data-tab="mqtt" onclick="switchLogTab('mqtt', this)">
                <i class="fas fa-broadcast-tower mr-1"></i>MQTT
            </div>
            <div class="log-tab" data-tab="sd" onclick="switchLogTab('sd', this)">
                <i class="fas fa-sd-card mr-1"></i>File SD
            </div>
            <div class="log-tab" data-tab="request" onclick="switchLogTab('request', this)">
                <i class="fas fa-exchange-alt mr-1"></i>Request
            </div>
            <div class="log-tab" data-tab="server" id="tab-server" onclick="switchLogTab('server', this)" style="display:none;">
                <i class="fas fa-server mr-1"></i>Server
            </div>
        </div>

        {{-- Welcome state --}}
        <div class="log-empty" id="log-welcome">
            <i class="fas fa-mouse-pointer fa-2x" style="color:#dee2e6;"></i>
            <span>Pilih sumber log dari panel kiri</span>
        </div>

        {{-- ── Tab MQTT ── --}}
        <div class="log-tab-pane" id="pane-mqtt">
            <div class="log-toolbar">
                <span id="mqtt-title" style="font-weight:600; font-size:12px;"></span>
                <input type="text" id="mqtt-search" class="form-control form-control-sm" style="width:200px;" placeholder="Cari payload...">
                <button class="btn btn-sm btn-outline-secondary" onclick="loadMqtt()"><i class="fas fa-sync"></i></button>
                <button class="btn btn-sm btn-outline-primary" onclick="copyLog('mqtt-content')"><i class="fas fa-copy mr-1"></i>Salin</button>
                <span id="mqtt-total" class="text-muted ml-auto" style="font-size:11px;"></span>
            </div>
            <div class="log-content" id="mqtt-content">
                <div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>
            </div>
            <div class="log-pagination" id="mqtt-pagination"></div>
            <div class="log-actions">
                <form method="POST" action="{{ route('log.device.clear') }}" onsubmit="return confirm('Hapus log device ini?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="device" id="mqtt-delete-device">
                    <input type="hidden" name="tanggal" id="mqtt-delete-tanggal">
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash mr-1"></i>Hapus Tanggal Ini</button>
                </form>
                <form method="POST" action="{{ route('log.device.clear') }}" onsubmit="return confirm('Hapus semua log device ini?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="device" id="mqtt-delete-device2">
                    <input type="hidden" name="all" value="1">
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash mr-1"></i>Hapus Semua</button>
                </form>
            </div>
        </div>

        {{-- ── Tab File SD ── --}}
        <div class="log-tab-pane" id="pane-sd">
            <div class="log-toolbar">
                <span id="sd-title" style="font-weight:600; font-size:12px;"></span>
                <span id="sd-size" class="text-muted" style="font-size:11px;"></span>
                <button class="btn btn-sm btn-outline-primary ml-auto" onclick="copyLog('sd-content')"><i class="fas fa-copy mr-1"></i>Salin</button>
            </div>
            <div class="log-content" id="sd-content">
                <div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>
            </div>
        </div>

        {{-- ── Tab Request ── --}}
        <div class="log-tab-pane" id="pane-request">
            <div class="log-toolbar">
                <span id="request-title" style="font-weight:600; font-size:12px;"></span>
                <input type="text" id="request-search" class="form-control form-control-sm" style="width:200px;" placeholder="Cari detail...">
                <button class="btn btn-sm btn-outline-secondary" onclick="loadRequest()"><i class="fas fa-sync"></i></button>
                <button class="btn btn-sm btn-outline-primary" onclick="copyLog('request-content')"><i class="fas fa-copy mr-1"></i>Salin</button>
                <span id="request-total" class="text-muted ml-auto" style="font-size:11px;"></span>
            </div>
            <div class="log-content" id="request-content">
                <div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>
            </div>
            <div class="log-pagination" id="request-pagination"></div>
            <div class="log-actions">
                <form method="POST" action="{{ route('log.tempreq.clear') }}" onsubmit="return confirm('Hapus request log tanggal ini?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="tanggal" id="request-delete-tanggal">
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash mr-1"></i>Hapus Tanggal Ini</button>
                </form>
                <form method="POST" action="{{ route('log.tempreq.clear') }}" onsubmit="return confirm('Hapus semua request log?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="all" value="1">
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash mr-1"></i>Hapus Semua</button>
                </form>
            </div>
        </div>

        {{-- ── Tab Server ── --}}
        <div class="log-tab-pane" id="pane-server">
            <div class="log-toolbar">
                <span id="server-title" style="font-weight:600; font-size:12px;"></span>
                <span id="server-size" class="text-muted" style="font-size:11px;"></span>
                <button class="btn btn-sm btn-outline-secondary" onclick="loadServer(currentServerFile)"><i class="fas fa-sync"></i></button>
                <button class="btn btn-sm btn-outline-primary ml-auto" onclick="copyLog('server-content')"><i class="fas fa-copy mr-1"></i>Salin</button>
            </div>
            <div class="log-content" id="server-content">
                <div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// ── State ──
let currentDevice   = null;
let currentTanggal  = null;
let currentType     = null; // mqtt | sd | request | server
let currentServerFile = null;
let mqttPage = 1;
let requestPage = 1;

// ── Load sidebar ──
async function loadSidebar() {
    const res = await fetch('{{ route("log.sidebar") }}', {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const data = await res.json();
    renderSidebar(data);
}

function renderSidebar(data) {
    window._sidebarData = data;
    let html = '';

    // ── MQTT Log ──
    html += `<div class="log-section-title"><i class="fas fa-broadcast-tower mr-1"></i>MQTT Log</div>`;
    for (const [device, dates] of Object.entries(data.mqtt)) {
        html += `<div class="log-device-group">
            <div class="log-device-header" onclick="toggleDevice(this, '${device}', 'mqtt')">
                <i class="fas fa-microchip" style="color:#4a90d9;"></i> ${device}
                <i class="fas fa-chevron-right arrow"></i>
            </div>
            <div class="log-date-list" id="mqtt-dates-${device}">`;
        dates.forEach(d => {
            html += `<div class="log-date-item" onclick="selectMqtt('${device}', '${d.tanggal}', this)">
                <span>${d.tanggal}</span>
                <span class="log-badge">${d.total}</span>
            </div>`;
        });
        html += `</div></div>`;
    }

    // ── File SD ──
    html += `<div class="log-section-title" style="margin-top:8px;"><i class="fas fa-sd-card mr-1"></i>File SD</div>`;
    for (const [device, files] of Object.entries(data.sd)) {
        html += `<div class="log-device-group">
            <div class="log-device-header" onclick="toggleDevice(this, '${device}', 'sd')">
                <i class="fas fa-mobile-alt" style="color:#28a745;"></i> ${device}
                <i class="fas fa-chevron-right arrow"></i>
            </div>
            <div class="log-date-list" id="sd-dates-${device}">`;
        files.forEach(f => {
            const size = formatSize(f.size);
            html += `<div class="log-date-item" onclick="console.log('SD click:', '${f.filename}', '${device}'); selectSd('${f.filename}', '${device}', '${f.tanggal}', this)">
                <span>${f.tanggal}</span>
                <span class="log-badge">${size}</span>
            </div>`;
        });
        html += `</div></div>`;
    }

    // ── Request Log ──
    html += `<div class="log-section-title" style="margin-top:8px;"><i class="fas fa-exchange-alt mr-1"></i>Request Log</div>`;
    data.request.forEach(r => {
        html += `<div class="log-single-item" onclick="selectRequest('${r.tanggal}', this)">
            <i class="fas fa-calendar-day" style="color:#fd7e14;"></i>
            <span>${r.tanggal}</span>
            <span class="log-badge">${r.total}</span>
        </div>`;
    });

    // ── Server Log ──
    html += `<div class="log-section-title" style="margin-top:8px;"><i class="fas fa-server mr-1"></i>Server Log</div>`;
    data.server.forEach(f => {
        if (f.size === 0) return;
        html += `<div class="log-single-item" onclick="selectServer('${f.filename}', '${f.tanggal}', this)">
            <i class="fas fa-file-alt" style="color:#6f42c1;"></i>
            <span>${f.tanggal}</span>
            <span class="log-badge">${formatSize(f.size)}</span>
        </div>`;
    });

    document.getElementById('sidebar-content').innerHTML = html;
}

// ── Toggle device expand ──
function toggleDevice(el, device, type) {
    const listId = `${type}-dates-${device}`;
    const list   = document.getElementById(listId);
    el.classList.toggle('open');
    list.classList.toggle('open');
}

// ── Select MQTT ──
function selectMqtt(device, tanggal, el) {
    setActiveItem(el);
    currentDevice  = device;
    currentTanggal = tanggal;
    currentType    = 'mqtt';
    mqttPage       = 1;
    document.getElementById('mqtt-delete-device').value  = device;
    document.getElementById('mqtt-delete-device2').value = device;
    document.getElementById('mqtt-delete-tanggal').value = tanggal;
    showMainPanel('mqtt');
    document.getElementById('mqtt-title').textContent = `${device} — ${tanggal}`;
    loadMqtt();
}

async function loadMqtt() {
    const search = document.getElementById('mqtt-search').value;
    document.getElementById('mqtt-content').innerHTML = '<div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>';
    const res = await fetch(`{{ route("log.ajax.mqtt") }}?device=${currentDevice}&tanggal=${currentTanggal}&page=${mqttPage}`, {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const data = await res.json();
    document.getElementById('mqtt-total').textContent = `${data.total} records`;
    let html = '<table class="log-table"><thead><tr><th>Waktu</th><th>Topic</th><th>Payload</th></tr></thead><tbody>';
    if (!data.data.length) {
        html = '<div class="log-empty"><i class="fas fa-inbox fa-2x" style="color:#dee2e6;"></i><span>Tidak ada data</span></div>';
    } else {
        data.data.forEach(r => {
            const payload = r.payload ? r.payload.substring(0, 200) : '-';
            html += `<tr>
                <td style="white-space:nowrap;">${r.received_at}</td>
                <td style="color:#4a90d9;">${r.topic ?? '-'}</td>
                <td style="word-break:break-all;">${escHtml(payload)}</td>
            </tr>`;
        });
        html += '</tbody></table>';
    }
    document.getElementById('mqtt-content').innerHTML = html;
    renderPagination('mqtt-pagination', data.current, data.lastPage, (p) => { mqttPage = p; loadMqtt(); });
}

// ── Select File SD ──
function selectSd(filename, device, tanggal, el) {
    setActiveItem(el);
    currentType = 'sd';
    showMainPanel('sd');
    document.getElementById('sd-title').textContent = `${device} — ${tanggal}`;
    loadSdFile(filename);
}

async function loadSdFile(filename) {
    document.getElementById('sd-content').innerHTML = '<div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>';
    const res = await fetch(`{{ route("log.file.read") }}?f=${encodeURIComponent(filename)}`, {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const text = await res.text();
    const lines = text.split('\n');
    let html = '<div class="log-pre">';
    lines.forEach(line => {
        const esc = escHtml(line);
        if (line.includes('gagal') || line.includes('Gagal') || line.includes('ERROR') || line.includes('error')) {
            html += `<span class="log-error">${esc}</span>\n`;
        } else if (line.includes('sukses') || line.includes('Sukses') || line.includes('berhasil')) {
            html += `<span style="color:#28a745;">${esc}</span>\n`;
        } else {
            html += esc + '\n';
        }
    });
    html += '</div>';
    document.getElementById('sd-content').innerHTML = html;
    document.getElementById('sd-size').textContent = `${lines.length} baris`;
}

// ── Select Request ──
function selectRequest(tanggal, el) {
    setActiveItem(el);
    currentTanggal  = tanggal;
    currentType     = 'request';
    requestPage     = 1;
    document.getElementById('request-delete-tanggal').value = tanggal;
    showMainPanel('request');
    document.getElementById('request-title').textContent = `Request Log — ${tanggal}`;
    loadRequest();
}

async function loadRequest() {
    document.getElementById('request-content').innerHTML = '<div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>';
    const search = document.getElementById('request-search').value;
    const res = await fetch(`{{ route("log.ajax.request") }}?tanggal=${currentTanggal}&page=${requestPage}&q=${encodeURIComponent(search)}`, {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const data = await res.json();
    document.getElementById('request-total').textContent = `${data.total} records`;
    let html = '<table class="log-table"><thead><tr><th>Timestamp</th><th>IP</th><th>Jenis</th><th>Detail</th></tr></thead><tbody>';
    if (!data.data.length) {
        html = '<div class="log-empty"><i class="fas fa-inbox fa-2x" style="color:#dee2e6;"></i><span>Tidak ada data</span></div>';
    } else {
        data.data.forEach(r => {
            html += `<tr>
                <td style="white-space:nowrap;">${r.timestamp}</td>
                <td>${r.ip ?? '-'}</td>
                <td><span class="badge badge-secondary">${r.info ?? '-'}</span></td>
                <td style="word-break:break-all; max-width:400px;">${escHtml((r.detail ?? '').substring(0, 300))}</td>
            </tr>`;
        });
        html += '</tbody></table>';
    }
    document.getElementById('request-content').innerHTML = html;
    renderPagination('request-pagination', data.current, data.lastPage, (p) => { requestPage = p; loadRequest(); });
}

// ── Select Server ──
function selectServer(filename, tanggal, el) {
    setActiveItem(el);
    currentServerFile = filename;
    currentType = 'server';
    document.getElementById('tab-server').style.display = 'block';
    showMainPanel('server');
    document.getElementById('server-title').textContent = `Server Log — ${tanggal}`;
    loadServer(filename);
}

async function loadServer(filename) {
    document.getElementById('server-content').innerHTML = '<div class="log-loading"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>';
    const res = await fetch(`{{ route("log.ajax.server") }}?file=${encodeURIComponent(filename)}`, {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.error) {
        document.getElementById('server-content').innerHTML = `<div class="log-empty"><span>${data.error}</span></div>`;
        return;
    }
    document.getElementById('server-size').textContent = `${formatSize(data.size)} — ${data.lines.length} baris terakhir`;
    let html = '<div class="log-pre">';
    data.lines.forEach(line => {
        if (!line) return;
        const esc = escHtml(line);
        if (line.includes('.ERROR') || line.includes('ERROR')) {
            html += `<span class="log-error">${esc}</span>`;
        } else if (line.includes('.WARNING') || line.includes('WARNING')) {
            html += `<span class="log-warning">${esc}</span>`;
        } else if (line.includes('.INFO') || line.includes('INFO')) {
            html += `<span class="log-info">${esc}</span>`;
        } else {
            html += esc;
        }
    });
    html += '</div>';
    document.getElementById('server-content').innerHTML = html;
    // Scroll ke bawah
    const el = document.getElementById('server-content');
    el.scrollTop = el.scrollHeight;
}

// ── Show main panel ──
function showMainPanel(type) {
    document.getElementById('log-welcome').style.display = 'none';
    document.getElementById('log-tabs').style.display    = 'flex';
    document.querySelectorAll('.log-tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.log-tab').forEach(t => t.classList.remove('active'));
    document.getElementById(`pane-${type}`).classList.add('active');
    document.querySelector(`.log-tab[data-tab="${type}"]`).classList.add('active');
}

function switchLogTab(type, el) {
    if (!currentDevice && (type === 'mqtt' || type === 'sd')) return;
    if (type === 'request' && currentType !== 'request') {
        document.querySelectorAll('.log-tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.log-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('pane-request').classList.add('active');
        el.classList.add('active');

        // Auto-load request log dari tanggal yang sama
        if (currentTanggal && window._sidebarData) {
            const exists = window._sidebarData.request.find(r => r.tanggal === currentTanggal);
            if (exists) {
                currentType = 'request';
                requestPage = 1;
                document.getElementById('request-delete-tanggal').value = currentTanggal;
                document.getElementById('request-title').textContent = `Request Log — ${currentTanggal}`;
                loadRequest();
                return;
            }
        }
        document.getElementById('request-content').innerHTML = '<div class="log-empty"><i class="fas fa-hand-point-left fa-2x" style="color:#dee2e6;"></i><span>File tidak ada, Pilih file lain dari sidebar Request Log</span></div>';
        return;
    }
    if (type === 'server' && currentType !== 'server') return;
    if (type === 'sd' && currentType !== 'sd') {
        document.querySelectorAll('.log-tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.log-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('pane-sd').classList.add('active');
        el.classList.add('active');

        // Coba auto-load file SD terbaru dari device yang sama
        if (currentDevice && window._sidebarData && window._sidebarData.sd[currentDevice]) {
            const files = window._sidebarData.sd[currentDevice];
            if (files.length > 0) {
                const f = files[0]; // file terbaru
                currentType = 'sd';
                document.getElementById('sd-title').textContent = `${currentDevice} — ${f.tanggal}`;
                loadSdFile(f.filename);
                return;
            }
        }
        document.getElementById('sd-content').innerHTML = '<div class="log-empty"><i class="fas fa-hand-point-left fa-2x" style="color:#dee2e6;"></i><span>File Tidak ada, Pilih file lain dari sidebar File SD</span></div>';
        return;
    }
    document.querySelectorAll('.log-tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.log-tab').forEach(t => t.classList.remove('active'));
    document.getElementById(`pane-${type}`).classList.add('active');
    el.classList.add('active');
}

// ── Helpers ──
function setActiveItem(el) {
    document.querySelectorAll('.log-date-item, .log-single-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}

function renderPagination(containerId, current, last, callback) {
    if (last <= 1) { document.getElementById(containerId).innerHTML = ''; return; }
    let html = `<span class="text-muted">Hal ${current} / ${last}</span>`;
    if (current > 1) html += `<button class="btn btn-xs btn-outline-secondary" onclick="(${callback.toString()})(${current-1})">‹ Prev</button>`;
    if (current < last) html += `<button class="btn btn-xs btn-outline-secondary" onclick="(${callback.toString()})(${current+1})">Next ›</button>`;
    document.getElementById(containerId).innerHTML = html;
}

function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes/1024).toFixed(1) + ' KB';
    return (bytes/1048576).toFixed(1) + ' MB';
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Init ──
loadSidebar();

function copyLog(contentId) {
    const el = document.getElementById(contentId);
    const text = el.innerText;
    navigator.clipboard.writeText(text).then(() => {
        toastr.success('Log berhasil disalin!');
    }).catch(() => {
        // fallback
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        toastr.success('Log berhasil disalin!');
    });
}
</script>
@endpush