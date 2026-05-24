@extends('layouts.app')
@section('title', 'Log Device ' . $id)
@section('page_title', 'Log Device')

@push('styles')
<style>
.log-line { font-family: monospace; font-size: 12px; padding: 2px 8px; border-radius: 4px; margin-bottom: 1px; white-space: pre-wrap; word-break: break-all; }
.log-line.log-error   { background: #fff0f0; color: #c00; border-left: 3px solid #f44336; }
.log-line.log-success { background: #f0fff4; color: #1b5e20; border-left: 3px solid #4caf50; }
.log-line.log-warning { background: #fffde7; color: #e65100; border-left: 3px solid #ff9800; }
.log-line.log-info    { background: #e3f2fd; color: #0d47a1; border-left: 3px solid #2196f3; }
.log-line.log-normal  { background: #fafafa; color: #333; border-left: 3px solid #e0e0e0; }
.log-line.hidden-line { display: none; }
#log-container { max-height: 70vh; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 8px; padding: 8px; }
.badge-online  { background:#00c853; color:#fff; padding:3px 10px; border-radius:20px; font-size:11px; }
.badge-offline { background:#f44336; color:#fff; padding:3px 10px; border-radius:20px; font-size:11px; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center mb-3 flex-wrap" style="gap:8px;">
    <a href="{{ route('device') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i>Kembali
    </a>
    <h5 class="mb-0 ml-2">
        <i class="fas fa-microchip mr-1"></i>{{ $id }}
        @if($device)
            <span class="{{ $device->online ? 'badge-online' : 'badge-offline' }}">
                {{ $device->online ? 'Online' : 'Offline' }}
            </span>
        @endif
    </h5>
    <div class="ml-auto d-flex align-items-center" style="gap:8px;">
        {{-- Auto refresh toggle --}}
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="autoRefresh">
            <label class="custom-control-label" for="autoRefresh" style="font-size:12px;">Auto Refresh</label>
        </div>
        <span id="refresh-countdown" class="text-muted" style="font-size:11px; display:none;"></span>
    </div>
</div>

{{-- Filter --}}
<div class="card card-outline card-primary mb-3">
    <div class="card-body py-2">
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            {{-- Pilih tanggal --}}
            <select id="tanggal-select" class="form-control form-control-sm" style="width:160px;"
                onchange="goToDate(this.value)">
                @forelse($tanggalList as $tgl)
                    <option value="{{ $tgl }}" {{ $tgl == $tanggal ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') }}
                        {{ $tgl == date('Y-m-d') ? '(Hari ini)' : '' }}
                    </option>
                @empty
                    <option value="{{ $tanggal }}">{{ $tanggal }}</option>
                @endforelse
            </select>

            {{-- Prev / Next --}}
            @php
                $idx     = array_search($tanggal, $tanggalList);
                $prevTgl = ($idx !== false && $idx < count($tanggalList) - 1) ? $tanggalList[$idx + 1] : null;
                $nextTgl = ($idx !== false && $idx > 0) ? $tanggalList[$idx - 1] : null;
            @endphp
            <a href="{{ $prevTgl ? route('device.log', [$id, 'tanggal' => $prevTgl]) : '#' }}"
                class="btn btn-sm btn-outline-secondary {{ !$prevTgl ? 'disabled' : '' }}">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="{{ $nextTgl ? route('device.log', [$id, 'tanggal' => $nextTgl]) : '#' }}"
                class="btn btn-sm btn-outline-secondary {{ !$nextTgl ? 'disabled' : '' }}">
                <i class="fas fa-chevron-right"></i>
            </a>

            {{-- Search --}}
            <input type="text" id="searchLog" class="form-control form-control-sm"
                placeholder="🔍 Cari keyword..." style="width:200px;"
                oninput="searchLog(this.value)">

            {{-- Filter jenis --}}
            <select id="filterJenis" class="form-control form-control-sm" style="width:130px;"
                onchange="filterJenis(this.value)">
                <option value="semua">Semua</option>
                <option value="log-error">Error</option>
                <option value="log-warning">Warning</option>
                <option value="log-success">Sukses</option>
                <option value="log-info">Info</option>
            </select>

            <span class="text-muted ml-auto" style="font-size:12px;">
                <span id="line-count">{{ count($logLines) }}</span> baris
            </span>
        </div>
    </div>
</div>

{{-- Log Content --}}
<div id="log-container">
    @forelse($logLines as $line)
    @php
        $lower = strtolower($line);
        if (str_contains($lower, 'gagal') || str_contains($lower, 'error') || str_contains($lower, 'fail') || str_contains($lower, 'wdt'))
            $cls = 'log-error';
        elseif (str_contains($lower, 'sukses') || str_contains($lower, 'berhasil') || str_contains($lower, 'cleanup'))
            $cls = 'log-success';
        elseif (str_contains($lower, 'jadwal') || str_contains($lower, 'saatnya') || str_contains($lower, 'restart') || str_contains($lower, 'order'))
            $cls = 'log-warning';
        elseif (str_contains($lower, 'mqtt') || str_contains($lower, 'pesan') || str_contains($lower, 'perintah'))
            $cls = 'log-info';
        else
            $cls = 'log-normal';
    @endphp
    <div class="log-line {{ $cls }}" data-class="{{ $cls }}">{{ $line }}</div>
    @empty
    <div class="text-center text-muted py-4">
        <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
        Tidak ada log untuk tanggal {{ $tanggal }}
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
let activeJenis = 'semua';
let activeSearch = '';
let refreshTimer = null;
let refreshSeconds = 30;

function goToDate(tgl) {
    window.location.href = '{{ route('device.log', $id) }}?tanggal=' + tgl;
}

function searchLog(keyword) {
    activeSearch = keyword.toLowerCase();
    applyFilter();
}

function filterJenis(jenis) {
    activeJenis = jenis;
    applyFilter();
}

function applyFilter() {
    let count = 0;
    document.querySelectorAll('.log-line').forEach(el => {
        const matchJenis  = activeJenis === 'semua' || el.dataset.class === activeJenis;
        const matchSearch = !activeSearch || el.textContent.toLowerCase().includes(activeSearch);
        const show = matchJenis && matchSearch;
        el.classList.toggle('hidden-line', !show);
        if (show) count++;
    });
    document.getElementById('line-count').textContent = count;
}

// Auto refresh
document.getElementById('autoRefresh').addEventListener('change', function() {
    if (this.checked) {
        startRefresh();
        document.getElementById('refresh-countdown').style.display = 'inline';
    } else {
        stopRefresh();
        document.getElementById('refresh-countdown').style.display = 'none';
    }
});

function startRefresh() {
    let sec = refreshSeconds;
    document.getElementById('refresh-countdown').textContent = sec + 's';
    refreshTimer = setInterval(() => {
        sec--;
        document.getElementById('refresh-countdown').textContent = sec + 's';
        if (sec <= 0) {
            location.reload();
        }
    }, 1000);
}

function stopRefresh() {
    clearInterval(refreshTimer);
    refreshTimer = null;
}

// Scroll to bottom on load
window.addEventListener('load', () => {
    const c = document.getElementById('log-container');
    c.scrollTop = c.scrollHeight;
});
</script>
@endpush
