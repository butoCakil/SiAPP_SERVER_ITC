@extends('layouts.app')
@section('title', 'Detail Device ' . $id)
@section('page_title', 'Detail Device')

@push('styles')
<style>
.metric-card { border-radius:10px; padding:14px 18px; color:#fff; display:flex; align-items:center; gap:12px; box-shadow:0 4px 14px rgba(0,0,0,0.1); }
.metric-card .m-val { font-size:1.8em; font-weight:700; line-height:1; }
.metric-card .m-lbl { font-size:11px; opacity:0.85; }
.mc-ram   { background:linear-gradient(135deg,#2196f3,#0d47a1); }
.mc-rssi  { background:linear-gradient(135deg,#ff9800,#e65100); }
.mc-ping  { background:linear-gradient(135deg,#4caf50,#1b5e20); }
.mc-buf   { background:linear-gradient(135deg,#9c27b0,#4a148c); }
.tab-btn { padding:8px 18px; border-radius:20px; border:2px solid #ddd; background:#fff; font-size:13px; cursor:pointer; font-weight:600; transition:all 0.2s; }
.tab-btn.active { border-color:#007bff; background:#007bff; color:#fff; }
.tab-pane { display:none; }
.tab-pane.active { display:block; }
.info-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f0f0f0; font-size:13px; }
.info-row:last-child { border-bottom:none; }
.info-label { color:#666; font-weight:500; }
.badge-online  { background:#00c853; color:#fff; padding:3px 12px; border-radius:20px; font-size:12px; }
.badge-offline { background:#f44336; color:#fff; padding:3px 12px; border-radius:20px; font-size:12px; }
.ctrl-section { margin-bottom:16px; }
.ctrl-section h6 { font-weight:700; color:#333; border-bottom:2px solid #e0e0e0; padding-bottom:6px; margin-bottom:12px; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center mb-3 flex-wrap" style="gap:8px;">
    <a href="{{ route('device') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i>Kembali
    </a>
    <h5 class="mb-0 ml-2">
        <i class="fas fa-microchip mr-1"></i>{{ $id }}
        @if($device->fw_version)
            <span class="badge badge-warning ml-1">{{ $device->fw_version }}</span>
        @endif
        <span class="ml-2 {{ $device->online ? 'badge-online' : 'badge-offline' }}">
            {{ $device->online ? 'Online' : 'Offline' }}
        </span>
    </h5>
    <div class="ml-auto d-flex" style="gap:6px;">
        <button class="btn btn-sm btn-primary" onclick="kirimPerintah('setSetting')" {{ !$device->online ? 'disabled' : '' }}>
            <i class="fas fa-cog mr-1"></i>Set
        </button>
        <button class="btn btn-sm btn-success" onclick="kirimPerintah('upload')" {{ !$device->online ? 'disabled' : '' }}>
            <i class="fas fa-upload mr-1"></i>Upload
        </button>
        <button class="btn btn-sm btn-info" onclick="kirimPerintah('sync')" {{ !$device->online ? 'disabled' : '' }}>
            <i class="fas fa-sync mr-1"></i>Sync
        </button>
        <button class="btn btn-sm btn-danger" onclick="kirimPerintah('reboot')">
            <i class="fas fa-redo mr-1"></i>Reboot
        </button>
    </div>
</div>

{{-- Metric Cards --}}
@php
    $ram     = (int)($status['ram'] ?? 0);
    $rssi    = (int)($status['rssi'] ?? -100);
    $rssiPct = round(((max(-100, min(-40, $rssi)) + 100) / 60) * 100, 1);
    $ping    = (int)($status['latency'] ?? 0);
    $buffer  = isset($status['count']) ? (int)$status['count'] : null;
@endphp
<div class="row mb-3">
    <div class="col-6 col-md-3 mb-2">
        <div class="metric-card mc-ram">
            <div style="font-size:1.6em;">💾</div>
            <div><div class="m-val">{{ $ram }}%</div><div class="m-lbl">RAM</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-2">
        <div class="metric-card mc-rssi">
            <div style="font-size:1.6em;">🛜</div>
            <div><div class="m-val">{{ $rssiPct }}%</div><div class="m-lbl">RSSI ({{ $rssi }} dB)</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-2">
        <div class="metric-card mc-ping">
            <div style="font-size:1.6em;">⏳</div>
            <div><div class="m-val">{{ $ping }} ms</div><div class="m-lbl">Ping</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-2">
        <div class="metric-card mc-buf">
            <div style="font-size:1.6em;">🗂️</div>
            <div><div class="m-val">{{ $buffer ?? '-' }}</div><div class="m-lbl">Buffer</div></div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <canvas id="chart-metrics" height="80"></canvas>
    </div>
</div>

{{-- Tabs --}}
<div class="d-flex mb-3" style="gap:8px; flex-wrap:wrap;">
    <button class="tab-btn active" onclick="switchTab('info', this)">📋 Info</button>
    <button class="tab-btn" onclick="switchTab('kontrol', this)">⚙️ Kontrol</button>
    <button class="tab-btn" onclick="switchTab('log', this)">📜 Log</button>
</div>

{{-- Tab: Info --}}
<div class="tab-pane active" id="tab-info">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2"><strong>📡 Status Terakhir</strong></div>
                <div class="card-body">
                    <div class="info-row"><span class="info-label">Status</span><span>{{ $status['status'] ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">SSID</span><span>{{ $status['ssid'] ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Serial</span><span>{{ isset($status['serial']) ? ($status['serial'] ? 'ON' : 'OFF') : '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Firmware</span><span>{{ $status['version'] ?? $device->fw_version ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Last Seen</span><span>{{ $device->last_seen ?? '-' }}</span></div>
                    <div class="info-row"><span class="info-label">Online Since</span><span>{{ $device->online_since ?? '-' }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2"><strong>⚙️ Setting Terakhir</strong></div>
                <div class="card-body">
                    @if(!empty($setting))
                        @php $det = $setting['detail'] ?? []; @endphp
                        <div class="info-row"><span class="info-label">Mode</span><span>{{ $det['mode'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Jam Masuk</span><span>{{ $det['waktumasuk'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Jam Pulang</span><span>{{ $det['waktupulang'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Upload #1</span><span>{{ $det['wa'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Upload #2</span><span>{{ $det['wtp'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Timestamp</span><span>{{ $setting['timestamp'] ?? '-' }}</span></div>
                    @else
                        <p class="text-muted">Belum ada setting terkirim.</p>
                    @endif
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header py-2"><strong>💻 Command Terakhir</strong></div>
                <div class="card-body">
                    @if(!empty($command))
                        <div class="info-row"><span class="info-label">Status</span><span>{{ $command['status'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Detail</span><span>{{ $command['detail'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Timestamp</span><span>{{ $command['timestamp'] ?? '-' }}</span></div>
                    @else
                        <p class="text-muted">Belum ada command terkirim.</p>
                    @endif
                </div>
            </div>

            @php $koneksi = json_decode($device->last_koneksi, true) ?? []; @endphp
            @if(!empty($koneksi))
            <div class="card mt-2">
                <div class="card-header py-2"><strong>🔌 Koneksi Terakhir Dikirim</strong></div>
                <div class="card-body">
                    @if($koneksi['wifi_nama'] ?? null)
                    <div class="info-row"><span class="info-label">WiFi</span><span>{{ $koneksi['wifi_nama'] }} (index {{ $koneksi['wifi_index'] }})</span></div>
                    @endif
                    @if($koneksi['upload_nama'] ?? null)
                    <div class="info-row"><span class="info-label">URL Upload</span><span>{{ $koneksi['upload_nama'] }} (index {{ $koneksi['upload_index'] }})</span></div>
                    @endif
                    @if($koneksi['up1'] ?? null)
                    <div class="info-row"><span class="info-label">Upload #1</span><span>{{ $koneksi['up1'] }}</span></div>
                    @endif
                    @if($koneksi['up2'] ?? null)
                    <div class="info-row"><span class="info-label">Upload #2</span><span>{{ $koneksi['up2'] }}</span></div>
                    @endif
                    @if($koneksi['rs1'] ?? null)
                    <div class="info-row"><span class="info-label">Restart #1</span><span>{{ $koneksi['rs1'] }}</span></div>
                    @endif
                    @if($koneksi['rs2'] ?? null)
                    <div class="info-row"><span class="info-label">Restart #2</span><span>{{ $koneksi['rs2'] }}</span></div>
                    @endif
                    <div class="info-row"><span class="info-label">Dikirim</span><span>{{ $koneksi['timestamp'] }}</span></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($reg)
    <div class="card mt-2">
        <div class="card-header py-2"><strong>📝 Registrasi</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="info-row"><span class="info-label">Chip ID</span><span>{{ $reg->chip_id }}</span></div></div>
                <div class="col-md-3"><div class="info-row"><span class="info-label">No Device</span><span>{{ $reg->no_device }}</span></div></div>
                <div class="col-md-3"><div class="info-row"><span class="info-label">Kode</span><span>{{ $reg->kode }}</span></div></div>
                <div class="col-md-3"><div class="info-row"><span class="info-label">Info</span><span>{{ $reg->info_device }}</span></div></div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Tab: Kontrol --}}
<div class="tab-pane" id="tab-kontrol">
    {{-- Perintah Cepat --}}
    <div class="card mb-3">
        <div class="card-header py-2"><strong><i class="fas fa-terminal mr-1"></i>Perintah Cepat</strong></div>
        <div class="card-body">
            <div class="d-flex flex-wrap" style="gap:8px;">
                <button class="btn btn-primary" onclick="kirimPerintah('setSetting')" {{ !$device->online ? 'disabled' : '' }}>⚙️ Kirim Setting</button>
                <button class="btn btn-success" onclick="kirimPerintah('upload')" {{ !$device->online ? 'disabled' : '' }}>📤 Upload Presensi</button>
                <button class="btn btn-info" onclick="kirimPerintah('sync')" {{ !$device->online ? 'disabled' : '' }}>🔄 Sync DB</button>
                <button class="btn btn-warning" onclick="kirimPerintah('toggleSerial')" {{ !$device->online ? 'disabled' : '' }}>🔍 Toggle Serial</button>
                <button class="btn btn-danger" onclick="kirimPerintah('reboot')">🔁 Reboot</button>
            </div>
        </div>
    </div>

    {{-- Koneksi Device --}}
    <div class="card mb-3">
        <div class="card-header py-2"><strong><i class="fas fa-network-wired mr-1"></i>Koneksi & Jadwal Device</strong></div>
        <div class="card-body">
            <form action="{{ route('device.koneksi', $id) }}" method="POST">
                @csrf
                <div class="row">
                    {{-- WiFi Preset --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:12px;"><i class="fas fa-wifi mr-1"></i>WiFi Preset
                                @php $lk = json_decode($device->last_koneksi, true) ?? []; @endphp
                                @if($lk['wifi_nama'] ?? null)
                                    <span class="text-muted ml-1" style="font-size:10px;">(aktif: {{ $lk['wifi_nama'] }})</span>
                                @endif
                            </label>
                            <select name="wifi_index" class="form-control form-control-sm">
                                <option value="">— Tidak diubah —</option>
                                @php
                                $wifiPresets = [
                                    0 => 'Instruktur-TE',
                                    1 => 'Instruktur-MM',
                                    2 => 'WIFI-RFID-13',
                                    3 => 'WIFI-RFID-14',
                                    4 => 'WIFI-RFID-152',
                                    5 => 'HOTSPOT-SKANEBA',
                                    6 => 'HOTSPOT-SISWA',
                                    7 => 'HOTSPOT-SKANEBA-ITC',
                                    8 => 'mqtt',
                                    9 => 'bumblebee',
                                ];
                                @endphp
                                @foreach($wifiPresets as $idx => $nama)
                                    <option value="{{ $idx }}">{{ $idx }} — {{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- URL Upload Preset --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:12px;"><i class="fas fa-upload mr-1"></i>URL Upload Preset
                                @if($lk['upload_nama'] ?? null)
                                    <span class="text-muted ml-1" style="font-size:10px;">(aktif: {{ $lk['upload_nama'] }})</span>
                                @endif
                            </label>
                            <select name="upload_index" class="form-control form-control-sm">
                                <option value="">— Tidak diubah —</option>
                                @php
                                $uploadPresets = [
                                    0 => 'upload Presensi',
                                    1 => 'upload Sholat',
                                    2 => 'upload Izin',
                                    3 => 'upload Izin Mens',
                                ];
                                @endphp
                                @foreach($uploadPresets as $idx => $nama)
                                    <option value="{{ $idx }}">{{ $idx }} — {{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <label style="font-size:12px; font-weight:600;"><i class="fas fa-clock mr-1"></i>Jadwal Upload & Restart</label>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="font-size:11px;" class="text-muted">Upload #1</label>
                            <div class="d-flex" style="gap:4px;">
                                <input type="number" name="up1_h" class="form-control form-control-sm" placeholder="{{ isset($lk['up1']) ? substr($lk['up1'],0,2) : 'HH' }}" min="0" max="23" style="width:60px;">
                                <input type="number" name="up1_m" class="form-control form-control-sm" placeholder="{{ isset($lk['up1']) ? substr($lk['up1'],3,2) : 'MM' }}" min="0" max="59" style="width:60px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="font-size:11px;" class="text-muted">Upload #2</label>
                            <div class="d-flex" style="gap:4px;">
                                <input type="number" name="up2_h" class="form-control form-control-sm" placeholder="{{ isset($lk['up2']) ? substr($lk['up2'],0,2) : 'HH' }}" min="0" max="23" style="width:60px;">
                                <input type="number" name="up2_m" class="form-control form-control-sm" placeholder="{{ isset($lk['up2']) ? substr($lk['up2'],3,2) : 'MM' }}" min="0" max="59" style="width:60px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="font-size:11px;" class="text-muted">Restart #1</label>
                            <div class="d-flex" style="gap:4px;">
                                <input type="number" name="rs1_h" class="form-control form-control-sm" placeholder="{{ isset($lk['rs1']) ? substr($lk['rs1'],0,2) : 'HH' }}" min="0" max="23" style="width:60px;">
                                <input type="number" name="rs1_m" class="form-control form-control-sm" placeholder="{{ isset($lk['rs1']) ? substr($lk['rs1'],3,2) : 'MM' }}" min="0" max="59" style="width:60px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="font-size:11px;" class="text-muted">Restart #2</label>
                            <div class="d-flex" style="gap:4px;">
                                <input type="number" name="rs2_h" class="form-control form-control-sm" placeholder="{{ isset($lk['rs2']) ? substr($lk['rs2'],0,2) : 'HH' }}" min="0" max="23" style="width:60px;">
                                <input type="number" name="rs2_m" class="form-control form-control-sm" placeholder="{{ isset($lk['rs2']) ? substr($lk['rs2'],3,2) : 'MM' }}" min="0" max="59" style="width:60px;">
                            </div>
                        </div>
                    </div>
                </div>

                <small class="text-muted d-block mb-3">
                    <i class="fas fa-info-circle mr-1"></i>Kosongkan field yang tidak ingin diubah.
                </small>

                <button type="submit" class="btn btn-primary" {{ !$device->online ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane mr-1"></i>Kirim ke Device
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Tab: Log --}}
<div class="tab-pane" id="tab-log">
    <div class="card">
        <div class="card-body">
            @if(count($logDates) > 0)
                <div class="d-flex align-items-center mb-3" style="gap:8px;">
                    <select id="log-date-select" class="form-control form-control-sm" style="width:180px;">
                        @foreach($logDates as $d)
                            <option value="{{ $d }}">{{ \Carbon\Carbon::parse($d)->translatedFormat('d M Y') }}{{ $d == date('Y-m-d') ? ' (Hari ini)' : '' }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-primary" onclick="loadLog()">Tampilkan</button>
                    <a href="{{ route('device.log', $id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                        <i class="fas fa-external-link-alt mr-1"></i>Buka Penuh
                    </a>
                </div>
                <div id="inline-log" style="max-height:400px; overflow-y:auto; font-family:monospace; font-size:12px; background:#f8f9fa; border-radius:8px; padding:10px;">
                    <p class="text-muted">Pilih tanggal dan klik Tampilkan.</p>
                </div>
            @else
                <p class="text-muted"><i class="fas fa-file-alt mr-1"></i>Belum ada file log untuk device ini.</p>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Tab switching ──
function switchTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

// ── Kirim perintah ──
async function kirimPerintah(cmd) {
    const res = await fetch('/api/device/perintah', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Api-Key': '{{ DB::table("api")->where("jenis","device_token")->where("status","aktif")->value("kode_api") }}' },
        body: JSON.stringify({ device_id: '{{ $id }}', [cmd]: 1 })
    });
    const data = await res.json();
    alert(data.message ?? 'Perintah terkirim');
}

// ── Inline log loader ──
async function loadLog() {
    const tgl = document.getElementById('log-date-select').value;
    const res = await fetch('{{ route("log.file.read") }}?f=' + tgl + '_log_{{ $id }}.txt');
    const text = await res.text();
    const lines = text.split('\n').filter(l => l.trim());
    const container = document.getElementById('inline-log');
    container.innerHTML = lines.map(line => {
        const l = line.toLowerCase();
        let cls = '';
        if (l.includes('gagal') || l.includes('error') || l.includes('wdt')) cls = 'color:#c00;background:#fff0f0;';
        else if (l.includes('sukses') || l.includes('berhasil')) cls = 'color:#1b5e20;background:#f0fff4;';
        else if (l.includes('jadwal') || l.includes('saatnya') || l.includes('order')) cls = 'color:#e65100;background:#fffde7;';
        return `<div style="padding:1px 6px;border-radius:3px;margin-bottom:1px;${cls}">${line}</div>`;
    }).join('');
    container.scrollTop = container.scrollHeight;
}

// ── Chart metrics ──
@php
    $labels  = $metrics->map(fn($m) => substr($m->recorded_at, 11, 5))->toJson();
    $ramData = $metrics->map(fn($m) => $m->ram)->toJson();
    $rssiData = $metrics->map(fn($m) => round(((max(-100, min(-40, $m->rssi)) + 100) / 60) * 100, 1))->toJson();
    $pingData = $metrics->map(fn($m) => min(100, $m->ping))->toJson();
    $bufData  = $metrics->map(fn($m) => min(100, $m->buffer))->toJson();
@endphp
new Chart(document.getElementById('chart-metrics'), {
    type: 'line',
    data: {
        labels: {!! $labels !!},
        datasets: [
            { label: 'RAM%',  data: {!! $ramData !!},  borderColor:'#2196f3', backgroundColor:'rgba(33,150,243,0.08)', borderWidth:2, pointRadius:2, tension:0.3, fill:true },
            { label: 'RSSI%', data: {!! $rssiData !!}, borderColor:'#ff9800', backgroundColor:'rgba(255,152,0,0.08)',   borderWidth:2, pointRadius:2, tension:0.3, fill:false },
            { label: 'Ping%', data: {!! $pingData !!}, borderColor:'#4caf50', backgroundColor:'rgba(76,175,80,0.08)',   borderWidth:2, pointRadius:2, tension:0.3, fill:false },
            { label: 'Buf',   data: {!! $bufData !!},  borderColor:'#9c27b0', backgroundColor:'rgba(156,39,176,0.08)',  borderWidth:2, pointRadius:2, tension:0.3, fill:false },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position:'top', labels:{ font:{size:11}, boxWidth:14 } },
            datalabels: { display: false }
        },
        scales: {
            x: { ticks:{ font:{size:10}, maxTicksLimit:10 }, grid:{display:false} },
            y: { min:0, max:100, ticks:{ font:{size:10} }, grid:{ color:'rgba(0,0,0,0.05)' } }
        }
    }
});
</script>
@endpush
