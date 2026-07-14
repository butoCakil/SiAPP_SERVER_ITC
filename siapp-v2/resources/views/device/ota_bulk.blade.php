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
.badge-versi { background:#6c757d; color:#fff; font-size:11px; }
.fw-actions { display:flex; gap:6px; }
.fw-actions button { border:none; background:none; padding:2px 6px; font-size:13px; }
.fw-actions button:hover { color:#f0ad4e; }
.fw-actions .btn-hapus-icon:hover { color:#dc3545; }
.fw-edit-panel, .fw-delete-panel { display:none; margin-top:8px; padding-top:8px; border-top:1px dashed #ddd; }
.fw-desc { font-size:12px; color:#555; margin-top:2px; }
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
                <div class="form-row">
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" id="ota-upload-versi" placeholder="Versi (mis. 1.5.7)" maxlength="50">
                    </div>
                    <div class="col-8">
                        <input type="text" class="form-control form-control-sm" id="ota-upload-deskripsi" placeholder="Deskripsi singkat (opsional)" maxlength="1000">
                    </div>
                </div>
                <small class="text-muted d-block mt-1">Maksimal 2MB.</small>
                <div id="upload-status" class="mt-2"></div>
            </div>
        </div>

        {{-- Daftar Firmware --}}
        <div class="card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <div>
                    <strong><i class="fas fa-archive mr-1"></i>Firmware Tersedia</strong>
                    <small class="text-muted ml-2">Klik untuk memilih</small>
                </div>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="auto-cleanup-toggle"
                        {{ $autoCleanup ? 'checked' : '' }} onchange="toggleAutoCleanup(this)">
                    <label class="custom-control-label" for="auto-cleanup-toggle" style="font-size:12px;">
                        Auto-hapus lama (keep 5)
                    </label>
                </div>
            </div>
            <div class="card-body p-2" id="firmware-list">
                @forelse($firmwareList as $fw)
                <div class="firmware-card" id="fw-card-{{ $loop->index }}" onclick="pilihFirmware('{{ $fw['filename'] }}', '{{ $fw['url'] }}', this)">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong style="font-size:13px;">{{ $fw['filename'] }}</strong>
                            @if($fw['versi'])
                                <span class="badge badge-versi ml-1">v{{ $fw['versi'] }}</span>
                            @endif
                            <br>
                            <small class="text-muted">{{ $fw['size'] }} &bull; {{ $fw['time'] }}</small>
                            @if($fw['deskripsi'])
                                <div class="fw-desc">{{ $fw['deskripsi'] }}</div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-warning mr-2" style="display:none;" id="check-{{ $loop->index }}"></i>
                            <div class="fw-actions" onclick="event.stopPropagation();">
                                <button type="button" title="Edit" onclick="toggleEditPanel({{ $loop->index }})">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="btn-hapus-icon" title="Hapus" onclick="toggleDeletePanel({{ $loop->index }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Panel Edit --}}
                    <div class="fw-edit-panel" id="fw-edit-{{ $loop->index }}" onclick="event.stopPropagation();">
                        <div class="form-row">
                            <div class="col-4">
                                <input type="text" class="form-control form-control-sm" id="edit-versi-{{ $loop->index }}"
                                    placeholder="Versi" maxlength="50" value="{{ $fw['versi'] }}">
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control form-control-sm" id="edit-deskripsi-{{ $loop->index }}"
                                    placeholder="Deskripsi" maxlength="1000" value="{{ $fw['deskripsi'] }}">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-primary"
                                onclick="saveFirmwareMeta('{{ $fw['filename'] }}', {{ $loop->index }})">Simpan</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="toggleEditPanel({{ $loop->index }})">Batal</button>
                        </div>
                    </div>

                    {{-- Panel Hapus --}}
                    <div class="fw-delete-panel" id="fw-delete-{{ $loop->index }}" onclick="event.stopPropagation();">
                        <small class="text-danger d-block mb-1">
                            Ketik ulang nama file <strong>{{ $fw['filename'] }}</strong> untuk konfirmasi hapus:
                        </small>
                        <input type="text" class="form-control form-control-sm" id="delete-confirm-{{ $loop->index }}"
                            placeholder="{{ $fw['filename'] }}" autocomplete="off"
                            oninput="checkDeleteConfirm({{ $loop->index }}, '{{ $fw['filename'] }}')">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-danger" id="btn-delete-confirm-{{ $loop->index }}"
                                disabled onclick="deleteFirmwareConfirmed('{{ $fw['filename'] }}', {{ $loop->index }})">
                                <i class="fas fa-trash mr-1"></i>Hapus Permanen
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="toggleDeletePanel({{ $loop->index }})">Batal</button>
                        </div>
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
        const check = c.querySelector('i.fa-check-circle');
        if (check) check.style.display = 'none';
    });
    el.classList.add('selected');
    el.querySelector('i.fa-check-circle').style.display = 'inline';

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
    formData.append('versi', document.getElementById('ota-upload-versi').value);
    formData.append('deskripsi', document.getElementById('ota-upload-deskripsi').value);
    formData.append('_token', '{{ csrf_token() }}');

    document.getElementById('upload-status').innerHTML =
        '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Mengupload...</span>';

    const res = await fetch('{{ route("device.ota.bulk.upload") }}', {
        method: 'POST', body: formData
    });
    const json = await res.json();

    if (json.status === 'ok') {
        document.getElementById('upload-status').innerHTML =
            '<span class="text-success"><i class="fas fa-check mr-1"></i>Upload berhasil!</span>';
        setTimeout(() => location.reload(), 1000);
    } else {
        document.getElementById('upload-status').innerHTML =
            '<span class="text-danger">Upload gagal.</span>';
    }
}

// Edit metadata firmware
function toggleEditPanel(idx) {
    document.getElementById('fw-delete-' + idx).style.display = 'none';
    const panel = document.getElementById('fw-edit-' + idx);
    panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
}

async function saveFirmwareMeta(filename, idx) {
    const versi     = document.getElementById('edit-versi-' + idx).value;
    const deskripsi = document.getElementById('edit-deskripsi-' + idx).value;

    const res = await fetch('{{ route("device.ota.bulk.meta") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ filename: filename, versi: versi, deskripsi: deskripsi })
    });
    const json = await res.json();

    if (json.status === 'ok') {
        location.reload();
    } else {
        alert('Gagal menyimpan: ' + (json.message ?? 'error'));
    }
}

// Hapus firmware (dengan konfirmasi ketik nama file)
function toggleDeletePanel(idx) {
    document.getElementById('fw-edit-' + idx).style.display = 'none';
    const panel = document.getElementById('fw-delete-' + idx);
    const willShow = panel.style.display !== 'block';
    panel.style.display = willShow ? 'block' : 'none';
    if (!willShow) {
        document.getElementById('delete-confirm-' + idx).value = '';
        document.getElementById('btn-delete-confirm-' + idx).disabled = true;
    }
}

function checkDeleteConfirm(idx, filename) {
    const val = document.getElementById('delete-confirm-' + idx).value;
    document.getElementById('btn-delete-confirm-' + idx).disabled = (val !== filename);
}

async function deleteFirmwareConfirmed(filename, idx) {
    const confirmVal = document.getElementById('delete-confirm-' + idx).value;

    const res = await fetch('{{ route("device.ota.bulk.delete") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ filename: filename, confirm_filename: confirmVal })
    });
    const json = await res.json();

    if (json.status === 'ok') {
        location.reload();
    } else {
        alert('Gagal menghapus: ' + (json.message ?? 'error'));
    }
}

// Toggle auto-cleanup
async function toggleAutoCleanup(checkbox) {
    const res = await fetch('{{ route("device.ota.bulk.autocleanup") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ enabled: checkbox.checked })
    });
    const json = await res.json();
    if (json.status !== 'ok') {
        alert('Gagal mengubah setting auto-cleanup.');
        checkbox.checked = !checkbox.checked;
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