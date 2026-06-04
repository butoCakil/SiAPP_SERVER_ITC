@extends('layouts.app')
@section('title', 'OTA Firmware Bulk')
@section('page_title', 'OTA Firmware Update')

@push('styles')
<style>
.firmware-card { border: 2px solid #e0e0e0; border-radius:8px; padding:12px; margin-bottom:8px; cursor:pointer; transition:all .2s; }
.firmware-card:hover { border-color:#f0ad4e; background:#fffdf0; }
.firmware-card.selected { border-color:#f0ad4e; background:#fff3cd; }
.device-row { padding:8px 12px; border-bottom:1px solid #f0f0f0; }
.device-row:last-child { border-bottom:none; }
.badge-online { background:#00c853; color:#fff; }
.badge-offline { background:#f44336; color:#fff; }
</style>
@endpush

@section('content')
<div class="row">

    {{-- Kolom Kiri: Upload + Pilih Firmware --}}
    <div class="col-md-5">

        {{-- Upload Firmware Baru --}}
        <div class="card">
            <div class="card-header py-2" style="background:#fff3cd;">
                <strong><i class="fas fa-upload mr-1"></i>Upload Firmware Baru</strong>
            </div>
            <div class="card-body">
                <div class="input-group mb-2">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="ota-file" accept=".bin">
                        <label class="custom-file-label" for="ota-file">Pilih file .bin...</label>
                    </div>
                    <div class="input-group-append">
                        <button class="btn btn-warning" onclick="otaUpload()">
                            <i class="fas fa-upload mr-1"></i>Upload
                        </button>
                    </div>
                </div>
                <small class="text-muted">Maksimal 2MB.</small>
                <div id="upload-status" class="mt-2"></div>
            </div>
        </div>

        {{-- Daftar Firmware --}}
        <div class="card">
            <div class="card-header py-2">
                <strong><i class="fas fa-archive mr-1"></i>Firmware Tersedia</strong>
                <small class="text-muted ml-2">Klik untuk memilih</small>
            </div>
            <div class="card-body p-2" id="firmware-list">
                @forelse($firmwareList as $fw)
                <div class="firmware-card" onclick="pilihFirmware('{{ $fw['filename'] }}', '{{ $fw['url'] }}', this)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong style="font-size:13px;">{{ $fw['filename'] }}</strong><br>
                            <small class="text-muted">{{ $fw['size'] }} &bull; {{ $fw['time'] }}</small>
                        </div>
                        <i class="fas fa-check-circle text-warning" style="display:none;" id="check-{{ $loop->index }}"></i>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">Belum ada firmware. Upload dulu.</p>
                @endforelse
            </div>
        </div>

        {{-- Info Firmware Terpilih --}}
        <div class="card" id="selected-fw-card" style="display:none;">
            <div class="card-header py-2" style="background:#d4edda;">
                <strong><i class="fas fa-check mr-1 text-success"></i>Firmware Terpilih</strong>
            </div>
            <div class="card-body py-2">
                <div id="selected-fw-info" style="font-size:13px; word-break:break-all;"></div>
            </div>
        </div>

    </div>

    {{-- Kolom Kanan: Pilih Device --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header py-2">
                <strong><i class="fas fa-mobile-alt mr-1"></i>Pilih Device</strong>
                <div class="float-right">
                    <button class="btn btn-xs btn-outline-success mr-1" onclick="pilihSemuaOnline()">
                        <i class="fas fa-check-double mr-1"></i>Pilih Semua Online
                    </button>
                    <button class="btn btn-xs btn-outline-secondary" onclick="batalSemua()">
                        <i class="fas fa-times mr-1"></i>Batal Semua
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($devices as $device)
                <div class="device-row d-flex align-items-center">
                    <input type="checkbox"
                        class="device-check mr-3"
                        value="{{ $device->device_id }}"
                        data-online="{{ $device->online }}"
                        id="dev-{{ $device->device_id }}"
                        {{ $device->online ? '' : 'disabled' }}>
                    <label for="dev-{{ $device->device_id }}" class="mb-0 flex-grow-1" style="cursor:pointer;">
                        <strong>{{ $device->device_id }}</strong>
                        <span class="badge {{ $device->online ? 'badge-online' : 'badge-offline' }} ml-2">
                            {{ $device->online ? 'Online' : 'Offline' }}
                        </span>
                        <span class="text-muted ml-2" style="font-size:12px;">
                            fw: {{ $device->fw_version ?? '-' }}
                        </span>
                    </label>
                </div>
                @endforeach
            </div>
            <div class="card-footer py-2">
                <span id="selected-count" class="text-muted" style="font-size:13px;">0 device dipilih</span>
                <button class="btn btn-danger float-right" id="btn-kirim" onclick="kirimOtaBulk()" disabled>
                    <i class="fas fa-bolt mr-1"></i>Kirim OTA ke <span id="btn-count">0</span> Device
                </button>
            </div>
        </div>

        {{-- Hasil Pengiriman --}}
        <div class="card" id="hasil-card" style="display:none;">
            <div class="card-header py-2" style="background:#d1ecf1;">
                <strong><i class="fas fa-list mr-1"></i>Hasil Pengiriman</strong>
            </div>
            <div class="card-body py-2" id="hasil-body"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('ota-file').addEventListener('change', function() {
    const label = this.nextElementSibling;
    label.textContent = this.files.length ? this.files[0].name : 'Pilih file .bin...';
});

let selectedFilename = null;
let selectedUrl      = null;

// Pilih firmware
function pilihFirmware(filename, url, el) {
    document.querySelectorAll('.firmware-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('i').style.display = 'none';
    });
    el.classList.add('selected');
    el.querySelector('i').style.display = 'inline';

    selectedFilename = filename;
    selectedUrl      = url;

    document.getElementById('selected-fw-card').style.display = 'block';
    document.getElementById('selected-fw-info').innerHTML =
        '<strong>' + filename + '</strong><br><small class="text-muted">' + url + '</small>';

    updateKirimBtn();
}

// Upload firmware baru
async function otaUpload() {
    const fileInput = document.getElementById('ota-file');
    if (!fileInput.files.length) { alert('Pilih file .bin terlebih dahulu.'); return; }

    const formData = new FormData();
    formData.append('firmware', fileInput.files[0]);
    formData.append('_token', '{{ csrf_token() }}');

    document.getElementById('upload-status').innerHTML =
        '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Mengupload...</span>';

    // Ambil device_id pertama untuk endpoint upload (tidak spesifik device)
    const res = await fetch('{{ route("device.ota.bulk.upload") }}', {
        method: 'POST', body: formData
    });
    const json = await res.json();

    if (json.status === 'ok') {
        document.getElementById('upload-status').innerHTML =
            '<span class="text-success"><i class="fas fa-check mr-1"></i>Upload berhasil!</span>';
        // Reload halaman untuk refresh daftar firmware
        setTimeout(() => location.reload(), 1000);
    } else {
        document.getElementById('upload-status').innerHTML =
            '<span class="text-danger">Upload gagal.</span>';
    }
}

// Pilih semua device online
function pilihSemuaOnline() {
    document.querySelectorAll('.device-check').forEach(cb => {
        if (cb.dataset.online == '1') cb.checked = true;
    });
    updateKirimBtn();
}

// Batal semua
function batalSemua() {
    document.querySelectorAll('.device-check').forEach(cb => cb.checked = false);
    updateKirimBtn();
}

// Update tombol kirim
function updateKirimBtn() {
    const count = document.querySelectorAll('.device-check:checked').length;
    document.getElementById('selected-count').textContent = count + ' device dipilih';
    document.getElementById('btn-count').textContent = count;
    document.getElementById('btn-kirim').disabled = (count === 0 || !selectedFilename);
}

document.querySelectorAll('.device-check').forEach(cb => {
    cb.addEventListener('change', updateKirimBtn);
});

// Kirim OTA bulk
async function kirimOtaBulk() {
    const deviceIds = Array.from(document.querySelectorAll('.device-check:checked')).map(cb => cb.value);
    if (!selectedFilename || !deviceIds.length) return;
    if (!confirm('Kirim OTA firmware "' + selectedFilename + '" ke ' + deviceIds.length + ' device?\nDevice akan restart dan flash firmware baru.')) return;

    document.getElementById('btn-kirim').disabled = true;
    document.getElementById('btn-kirim').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengirim...';

    const res = await fetch('{{ route("device.ota.bulk.send") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ filename: selectedFilename, device_ids: deviceIds })
    });
    const json = await res.json();

    document.getElementById('btn-kirim').disabled = false;
    document.getElementById('btn-kirim').innerHTML = '<i class="fas fa-bolt mr-1"></i>Kirim OTA ke <span id="btn-count">' + deviceIds.length + '</span> Device';

    // Tampilkan hasil
    let html = '';
    if (json.sent && json.sent.length) {
        html += '<p class="text-success mb-1"><i class="fas fa-check mr-1"></i><strong>Berhasil dikirim (' + json.sent.length + '):</strong> ' + json.sent.join(', ') + '</p>';
    }
    if (json.failed && json.failed.length) {
        html += '<p class="text-danger mb-1"><i class="fas fa-times mr-1"></i><strong>Gagal (' + json.failed.length + '):</strong> ' + json.failed.join(', ') + '</p>';
    }
    html += '<small class="text-muted">URL: ' + json.url + '</small>';

    document.getElementById('hasil-body').innerHTML = html;
    document.getElementById('hasil-card').style.display = 'block';
}
</script>
@endpush
