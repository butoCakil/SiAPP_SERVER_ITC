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
<div class="mb-3">
    {{-- Baris 1: Kembali + Nama + Status --}}
    <div class="d-flex align-items-center flex-wrap" style="gap:8px; margin-bottom:8px;">
        <a href="{{ route('device') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
        <h5 class="mb-0 ml-1">
            <i class="fas fa-microchip mr-1"></i>{{ $id }}
            @if($device->fw_version)
                <span class="badge badge-warning ml-1">{{ $device->fw_version }}</span>
            @endif
            <span class="ml-1 {{ $device->online ? 'badge-online' : 'badge-offline' }}">
                {{ $device->online ? 'Online' : 'Offline' }}
            </span>
        </h5>
    </div>
    {{-- Baris 2: Tombol aksi --}}
    <div class="d-flex flex-wrap" style="gap:6px;">
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
        <div style="position:relative; height:220px;">
            <canvas id="chart-metrics"></canvas>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="d-flex mb-3" style="gap:8px; flex-wrap:wrap;">
    <button class="tab-btn active" onclick="switchTab('info', this)">📋 Info</button>
    <button class="tab-btn" onclick="switchTab('kontrol', this)">⚙️ Kontrol</button>
    <button class="tab-btn" onclick="switchTab('log', this)">📜 Log</button>
    <button class="tab-btn" onclick="switchTab('filesd', this)">📁 File SD</button>
</div>

{{-- Tab: Info --}}
<div class="tab-pane active" id="tab-info">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2"><strong>⚙️ Setting Terakhir</strong></div>
                <div class="card-body">
                    @if(!empty($setting))
                        @php $det = $setting['detail'] ?? []; @endphp
                        <div class="info-row"><span class="info-label">Mode</span><span>{{ $det['mode'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Jam Masuk</span><span>{{ $det['waktumasuk'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Jam Pulang</span><span>{{ $det['waktupulang'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Upload #1</span><span>{{ $det['up1'] ?? ($det['wa'] ?? '-') }}</span></div>
                        <div class="info-row"><span class="info-label">Upload #2</span><span>{{ $det['up2'] ?? ($det['wtp'] ?? '-') }}</span></div>
                        <div class="info-row"><span class="info-label">Restart #1</span><span>{{ $det['rs1'] ?? '-' }}</span></div>
                        <div class="info-row"><span class="info-label">Restart #2</span><span>{{ $det['rs2'] ?? '-' }}</span></div>
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
                    @php
                        $otaStatus = $command['status'] ?? '-';
                        $otaBadge = match($otaStatus) {
                            'ota_ok'     => 'badge-success',
                            'ota_failed' => 'badge-danger',
                            'ota_start'  => 'badge-warning',
                            'ota_sent'   => 'badge-info',
                            default      => 'badge-secondary',
                        };
                    @endphp
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="badge {{ $otaBadge }}">{{ $otaStatus }}</span>
                        </div>
                        @if(!empty($command['detail']))
                        <div class="info-row">
                            <span class="info-label">Detail</span>
                            <span style="font-size:12px;word-break:break-all;">
                                {{ is_array($command['detail']) ? json_encode($command['detail']) : $command['detail'] }}
                            </span>
                        </div>
                        @endif
                        @if(!empty($command['version']))
                        <div class="info-row"><span class="info-label">Versi FW</span><span>{{ $command['version'] }}</span></div>
                        @endif
                        <div class="info-row"><span class="info-label">Waktu</span><span>{{ $command['timestamp'] ?? '-' }}</span></div>
                    @else
                        <p class="text-muted">Belum ada command terkirim.</p>
                    @endif
                </div>
            </div>
        </div>
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
                <button class="btn btn-warning" onclick="kirimPerintah('toggleSerial')" {{ !$device->online ? 'disabled' : '' }}>
                    🔍 Serial
                    @if(isset($status['serial']))
                        @if($status['serial'])
                            <span class="badge badge-success ml-1">ON</span>
                        @else
                            <span class="badge badge-secondary ml-1">OFF</span>
                        @endif
                    @else
                        <span class="badge badge-light ml-1">?</span>
                    @endif
                </button>
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

                {{-- URL DB Preset + Mode Device --}}
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:12px;"><i class="fas fa-database mr-1"></i>URL Database Preset
                                @if($lk['db_nama'] ?? null)
                                    <span class="text-muted ml-1" style="font-size:10px;">(aktif: {{ $lk['db_nama'] }})</span>
                                @endif
                            </label>
                            <select name="db_index" class="form-control form-control-sm">
                                <option value="">— Tidak diubah —</option>
                                @php
                                $dbPresets = [
                                    0 => 'fakeRestApi',
                                    1 => 'fakeRestApiMid',
                                    2 => 'restAPI/datasiswa',
                                    3 => 'restAPI/datagtk',
                                    4 => 'restAPI/data',
                                ];
                                @endphp
                                @foreach($dbPresets as $idx => $nama)
                                    <option value="{{ $idx }}">{{ $idx }} — {{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:12px;"><i class="fas fa-sliders-h mr-1"></i>Mode Device
                                @if(isset($lk['mode_nama']))
                                    <span class="text-muted ml-1" style="font-size:10px;">(aktif: {{ $lk['mode_nama'] }})</span>
                                @endif
                            </label>
                            <select name="mode_device" class="form-control form-control-sm">
                                <option value="">— Tidak diubah —</option>
                                <option value="0">0 — Normal (Presensi Siswa)</option>
                                <option value="1">1 — Sholat (Pembiasaan Sholat)</option>
                                <option value="2">2 — Full Online (Real-time)</option>
                            </select>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                Ubah mode harus sesuai URL Upload: Normal→upload Presensi, Sholat→upload Sholat.
                            </small>
                        </div>
                    </div>
                </div>
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

{{-- Tab: File SD --}}
<div class="tab-pane" id="tab-filesd">
    <div class="card">
        <div class="card-header py-2"><strong><i class="fas fa-sd-card mr-1"></i>File Explorer SD Card</strong></div>
        <div class="card-body">
            <div class="d-flex mb-3" style="gap:8px; align-items:center;">
                <input type="text" id="sd-path" class="form-control form-control-sm" value="/" style="max-width:300px;" placeholder="Path, contoh: /presensisholat">
                <button class="btn btn-sm btn-primary" onclick="sdListDir()">
                    <i class="fas fa-folder-open mr-1"></i>Lihat Isi
                </button>
                <span id="sd-status" class="text-muted" style="font-size:12px;"></span>
            </div>

            <div id="sd-result" style="display:none;">
                <div id="sd-dirs" class="mb-2"></div>
                <div id="sd-files"></div>
            </div>
            <div id="sd-loading" style="display:none;" class="text-muted">
                <i class="fas fa-spinner fa-spin mr-1"></i>Menunggu respon device...
            </div>
            <div id="sd-empty" class="text-muted" style="display:none;">Folder kosong atau tidak ada data.</div>
        </div>
    </div>
</div>


{{-- ── OTA Firmware ── --}}
<div class="card mt-3">
    <div class="card-header py-2" style="background:#fff3cd;">
        <strong><i class="fas fa-microchip mr-1"></i>OTA Firmware Update</strong>
        <span class="badge badge-secondary ml-2" style="font-size:11px;">
            Versi saat ini: <strong>{{ $device->fw_version ?? '-' }}</strong>
        </span>
    </div>
    <div class="card-body">
        <div class="ctrl-section">
            <h6>Upload File Firmware (.bin)</h6>
            <div class="input-group mb-2">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="ota-file" accept=".bin">
                    <label class="custom-file-label" for="ota-file">Pilih file .bin...</label>
                </div>
                <div class="input-group-append">
                    <button class="btn btn-warning" id="ota-upload-btn" onclick="otaUpload()">
                        <i class="fas fa-upload mr-1"></i>Upload
                    </button>
                </div>
            </div>
            <small class="text-muted">Maksimal 4MB. File akan disimpan di server.</small>
        </div>
        @if(!empty($firmwareList))
        <div class="ctrl-section">
            <h6>Atau Pilih Firmware yang Sudah Ada</h6>
            <div class="input-group">
                <select class="form-control form-control-sm" id="ota-existing" onchange="pilihFirmwareAda(this)">
                    <option value="">— Pilih firmware —</option>
                    @foreach($firmwareList as $fw)
                    <option value="{{ $fw['filename'] }}" data-url="{{ $fw['url'] }}">
                        {{ $fw['filename'] }} ({{ $fw['size'] }}, {{ $fw['time'] }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        <div class="ctrl-section" id="ota-send-section" style="display:none;">
            <h6>Kirim OTA ke Device</h6>
            <div class="alert alert-info py-2 mb-2" id="ota-url-info" style="font-size:12px;word-break:break-all;"></div>
            <button class="btn btn-danger" id="ota-send-btn" onclick="otaSend()">
                <i class="fas fa-bolt mr-1"></i>Kirim OTA ke Device
            </button>
            <small class="d-block mt-1 text-muted">Device akan download dan flash firmware secara otomatis.</small>
        </div>
        <div id="ota-status" class="mt-2"></div>
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
const DETAIL_DEVICE_KEY = '{{ DB::table("api")->where("jenis","device_token")->where("status","aktif")->value("kode_api") }}';
const DETAIL_DEVICE_ID  = '{{ $id }}';

async function kirimPerintah(cmd) {
    let pollData = {};
    try {
        const res  = await fetch('/api-internal/device-poll-status');
        const data = await res.json();
        data.forEach(d => { pollData[d.device_id] = d; });
    } catch(e) {}

    const fbKey  = CMD_FEEDBACK_KEY[cmd] ?? 'cmd_ts';
    const poll   = pollData[DETAIL_DEVICE_ID] ?? {};

    const deviceStates = [{
        device_id           : DETAIL_DEVICE_ID,
        online              : 1,
        skip                : false,
        state               : 'sending',
        ts_before           : poll[fbKey] ?? null,
        online_since        : poll.last_seen ?? null,
        fb_key              : fbKey,
        cmd_key             : cmd,
        info                : '{{ addslashes($device->info ? (json_decode($device->info,true)["label"] ?? $id) : $id) }}',
        deadline            : Date.now() + (cmd === 'reboot' ? 90000 : CMD_TIMEOUT_MS),
        sent_at             : Date.now(),
        reboot_confirmed_at : null,
    }];

    openCmdModal(cmd, deviceStates);

    // Kirim perintah
    await fetch('/api/device/perintah', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Device-Key': DETAIL_DEVICE_KEY },
        body: JSON.stringify({ device_id: DETAIL_DEVICE_ID, [cmd]: 1 })
    });

    deviceStates[0].state = 'pending';
    updateCmdRow(DETAIL_DEVICE_ID, 'pending', '⏳ Menunggu feedback...');
    updateCmdSummary(deviceStates);

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
        processPollingResult(d, current,
            (msg) => { d.state='ok'; updateCmdRow(d.device_id,'ok',msg); clearInterval(cmdModalTimer); updateCmdSummary(deviceStates); },
            ()    => { d.state='fail'; updateCmdRow(d.device_id,'fail','❌ Timeout'); clearInterval(cmdModalTimer); updateCmdSummary(deviceStates); },
            now
        );
        updateCmdSummary(deviceStates);
    }, CMD_POLL_MS);
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
            { label: 'RAM%',  data: {!! $ramData !!},  borderColor:'#2196f3', backgroundColor:'rgba(33,150,243,0.08)', borderWidth:1.5, pointRadius:0, pointHoverRadius:3, tension:0.3, fill:true },
            { label: 'RSSI%', data: {!! $rssiData !!}, borderColor:'#ff9800', backgroundColor:'rgba(255,152,0,0.08)',   borderWidth:1.5, pointRadius:0, pointHoverRadius:3, tension:0.3, fill:false },
            { label: 'Ping%', data: {!! $pingData !!}, borderColor:'#4caf50', backgroundColor:'rgba(76,175,80,0.08)',   borderWidth:1.5, pointRadius:0, pointHoverRadius:3, tension:0.3, fill:false },
            { label: 'Buf',   data: {!! $bufData !!},  borderColor:'#9c27b0', backgroundColor:'rgba(156,39,176,0.08)',  borderWidth:1.5, pointRadius:0, pointHoverRadius:3, tension:0.3, fill:false },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position:'top', labels:{ font:{size:11}, boxWidth:14 } },
            datalabels: { display: false }
        },
        scales: {
            x: { ticks:{ font:{size:10}, maxTicksLimit:6, maxRotation:0, autoSkip:true }, grid:{display:false} },
            y: { min:0, max:100, ticks:{ font:{size:10} }, grid:{ color:'rgba(0,0,0,0.05)' } }
        }
    }
});

// ── File SD Explorer ──
let sdPolling = null;

async function sdListDir() {
    const path = document.getElementById('sd-path').value || '/';
    document.getElementById('sd-loading').style.display = 'block';
    document.getElementById('sd-result').style.display = 'none';
    document.getElementById('sd-empty').style.display = 'none';
    document.getElementById('sd-status').textContent = 'Mengirim perintah...';

    // Kirim command listDir ke device
    await fetch('{{ route("device.listdir", $id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ path: path })
    });

    document.getElementById('sd-status').textContent = 'Menunggu respon device...';

    // Poll hasil tiap 1 detik, maks 10x
    let attempts = 0;
    sdPolling = setInterval(async () => {
        attempts++;
        const res = await fetch('{{ route("device.dirlist", $id) }}');
        const json = await res.json();

        if (json.status === 'ok' && json.data && json.data.path === path) {
            clearInterval(sdPolling);
            document.getElementById('sd-loading').style.display = 'none';
            document.getElementById('sd-status').textContent = 'Path: ' + json.data.path;
            renderSdResult(json.data);
        }

        if (attempts >= 10) {
            clearInterval(sdPolling);
            document.getElementById('sd-loading').style.display = 'none';
            document.getElementById('sd-status').textContent = 'Timeout — device tidak merespons.';
        }
    }, 1000);
}

function renderSdResult(data) {
    // ── Tombol navigasi ──
    const path = data.path;
    let navHtml = '<div class="d-flex align-items-center mb-2" style="gap:6px; flex-wrap:wrap;">';

    // Tombol kembali
    if (path !== '/') {
        const parts = path.replace(/\/$/, '').split('/');
        parts.pop();
        const parentPath = parts.length === 0 ? '/' : parts.join('/');
        navHtml += `<button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('sd-path').value='${parentPath}'; sdListDir();">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </button>`;
    }

    // Breadcrumb
    navHtml += '<span style="font-size:12px; color:#666;"><i class="fas fa-hdd mr-1"></i>';
    if (path === '/') {
        navHtml += '<strong>/</strong>';
    } else {
        navHtml += `<a href="#" onclick="document.getElementById('sd-path').value='/'; sdListDir(); return false;">/</a>`;
        const parts = path.replace(/^\//, '').replace(/\/$/, '').split('/');
        let cumPath = '';
        parts.forEach((p, i) => {
            cumPath += '/' + p;
            const cp = cumPath;
            if (i < parts.length - 1) {
                navHtml += ` / <a href="#" onclick="document.getElementById('sd-path').value='${cp}'; sdListDir(); return false;">${p}</a>`;
            } else {
                navHtml += ` / <strong>${p}</strong>`;
            }
        });
    }
    navHtml += '</span></div>';

    document.getElementById('sd-dirs').innerHTML = navHtml;

    const uploadPresets = {
        0: { name: 'upload Presensi',    url: 'http://172.16.80.123/data/uploadPresensi.php' },
        1: { name: 'upload Sholat',      url: 'http://172.16.80.123/data/uploadSholat.php' },
        2: { name: 'upload Izin',        url: 'http://172.16.80.123/data/uploadIzin.php' },
        3: { name: 'upload Izin Mens',   url: 'http://172.16.80.123/data/uploadIzinSholat.php' },
        4: { name: 'upload File (log)',  url: 'http://172.16.80.123/data/upload.php' },
    };

    // Render gabungan folder + file dalam satu tabel
    let tableHtml = '<table class="table table-sm table-hover mt-2" style="font-size:12px;">';
    tableHtml += '<thead class="thead-dark"><tr><th style="width:30px;"></th><th>Nama</th><th style="width:90px;">Ukuran</th><th>Upload ke</th><th style="width:80px;">Aksi</th></tr></thead><tbody>';

    // Folder dulu
    if (data.dirs && data.dirs.length > 0) {
        data.dirs.forEach(dir => {
            const fullPath = (data.path.endsWith('/') ? data.path : data.path + '/') + dir;
            tableHtml += `<tr style="cursor:pointer;" onclick="document.getElementById('sd-path').value='${fullPath}'; sdListDir();">
                <td><i class="fas fa-folder text-warning"></i></td>
                <td><strong>${dir}</strong></td>
                <td><span class="text-muted">—</span></td>
                <td></td>
                <td><span class="text-muted" style="font-size:10px;">Buka</span></td>
            </tr>`;
        });
    }

    // Lalu file
    if (data.files && data.files.length > 0) {
        data.files.forEach((f, i) => {
            const filePath = (data.path.endsWith('/') ? data.path : data.path + '/') + f.n;
            const size = f.s > 1024 ? (f.s / 1024).toFixed(1) + ' KB' : f.s + ' B';

            let selectHtml = '<select class="sd-upload-select form-control form-control-sm" style="font-size:11px;" onclick="event.stopPropagation();">';
            Object.entries(uploadPresets).forEach(([k, v]) => {
                selectHtml += `<option value="${v.url}">${v.name}</option>`;
            });
            selectHtml += '</select>';

            tableHtml += `<tr>
                <td><i class="fas fa-file-alt text-secondary"></i></td>
                <td>${f.n}</td>
                <td>${size}</td>
                <td>${selectHtml}</td>
                <td>
                    <button class="btn btn-xs btn-success" data-path="${filePath}" onclick="sdUploadFile(this.dataset.path, this.closest('tr').querySelector('.sd-upload-select').value)" style="font-size:11px;">
                        <i class="fas fa-upload"></i>
                    </button>
                </td>
            </tr>`;
        });
    }

    if ((!data.dirs || data.dirs.length === 0) && (!data.files || data.files.length === 0)) {
        tableHtml += '<tr><td colspan="5" class="text-muted text-center">Folder kosong</td></tr>';
    }

    tableHtml += '</tbody></table>';
    document.getElementById('sd-dirs').innerHTML += tableHtml;
    document.getElementById('sd-files').innerHTML = '';
    document.getElementById('sd-result').style.display = 'block';
}

async function sdUploadFile(path, url) {
    if (!confirm('Upload file ' + path + ' ke ' + url + '?')) return;
    document.getElementById('sd-status').textContent = 'Mengirim perintah upload...';
    const res = await fetch('{{ route("device.uploadfile", $id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ path: path, url: url })
    });
    const json = await res.json();
    document.getElementById('sd-status').textContent = json.message ?? 'Perintah terkirim';
}

let otaFilename = null;

// Update label custom file input
document.getElementById('ota-file').addEventListener('change', function() {
    const label = this.nextElementSibling;
    label.textContent = this.files.length ? this.files[0].name : 'Pilih file .bin...';
    // Reset pilihan firmware existing
    document.getElementById('ota-existing') && (document.getElementById('ota-existing').value = '');
});

function pilihFirmwareAda(select) {
    const filename = select.value;
    const url      = select.options[select.selectedIndex]?.dataset?.url;
    if (!filename) return;
    otaFilename = filename;
    document.getElementById('ota-url-info').textContent = 'URL: ' + url;
    document.getElementById('ota-send-section').style.display = 'block';
    document.getElementById('ota-status').innerHTML =
        '<span class="text-info"><i class="fas fa-check mr-1"></i>Firmware dipilih: ' + filename + '</span>';
}

async function otaUpload() {
    const fileInput = document.getElementById('ota-file');
    if (!fileInput.files.length) {
        alert('Pilih file .bin terlebih dahulu.');
        return;
    }
    const file = fileInput.files[0];
    if (!file.name.endsWith('.bin')) {
        alert('File harus berekstensi .bin');
        return;
    }

    document.getElementById('ota-upload-btn').disabled = true;
    document.getElementById('ota-status').innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Mengupload firmware...</span>';

    const formData = new FormData();
    formData.append('firmware', file);
    formData.append('_token', '{{ csrf_token() }}');

    const res = await fetch('{{ route("device.ota", $id) }}', {
        method: 'POST',
        body: formData
    });
    const json = await res.json();

    document.getElementById('ota-upload-btn').disabled = false;

    if (json.status === 'ok') {
        otaFilename = json.filename;
        document.getElementById('ota-url-info').textContent = 'URL: ' + json.url;
        document.getElementById('ota-send-section').style.display = 'block';
        document.getElementById('ota-status').innerHTML = '<span class="text-success"><i class="fas fa-check mr-1"></i>Upload berhasil: ' + json.filename + '</span>';
    } else {
        document.getElementById('ota-status').innerHTML = '<span class="text-danger">Upload gagal.</span>';
    }
}

async function otaSend() {
    if (!otaFilename) return;
    if (!confirm('Kirim OTA ke device {{ $id }}? Device akan restart dan flash firmware baru.')) return;

    document.getElementById('ota-send-btn').disabled = true;
    document.getElementById('ota-status').innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Mengirim perintah OTA...</span>';

    const res = await fetch('{{ route("device.ota.send", $id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ filename: otaFilename })
    });
    const json = await res.json();

    document.getElementById('ota-send-btn').disabled = false;

    if (json.status === 'ok') {
        document.getElementById('ota-status').innerHTML = '<span class="text-success"><i class="fas fa-bolt mr-1"></i>Perintah OTA terkirim. Tunggu device restart...</span>';
    } else {
        document.getElementById('ota-status').innerHTML = '<span class="text-danger">Gagal: ' + (json.message ?? 'error') + '</span>';
    }
}
</script>
@endpush
