@extends('layouts.app')
@section('title', 'Kalender Pendidikan')
@section('page_title', 'Kalender Pendidikan (Kaldik)')
@push('styles')
<style>
.kaldik-toolbar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
.kaldik-year { font-size:1.3em; font-weight:700; min-width:60px; text-align:center; }
.kaldik-grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; }
@media(max-width:768px) { .kaldik-grid { grid-template-columns: 1fr; } }
.kaldik-month { border:1px solid #dee2e6; border-radius:8px; overflow:hidden; }
.kaldik-month-title { background:#343a40; color:#fff; padding:8px 12px; font-weight:600; font-size:13px; }
.kaldik-cal { width:100%; border-collapse:collapse; }
.kaldik-cal th { font-size:10px; text-align:center; padding:4px 2px; background:#f8f9fa; color:#666; }
.kaldik-cal td { font-size:11px; text-align:center; padding:3px 2px; cursor:pointer; position:relative; vertical-align:top; min-height:32px; }
.kaldik-cal td:hover { background:#e3f2fd; }
.kaldik-cal td.minggu { color:#e53935; }
.kaldik-cal td.sabtu { color:#e53935; }
.kaldik-cal td.libur { background:#ffebee !important; }
.kaldik-cal td.kegiatan { background:#e3f2fd !important; }
.kaldik-cal td.today { font-weight:700; border:2px solid #007bff; border-radius:4px; }
.kaldik-dots { display:flex; flex-wrap:wrap; justify-content:center; gap:2px; margin-top:2px; }
.kaldik-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.kaldik-badge { display:inline-block; font-size:9px; padding:1px 5px; border-radius:10px; color:#fff; margin-top:2px; max-width:90%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.legend-row { display:flex; gap:12px; flex-wrap:wrap; font-size:12px; margin-bottom:12px; }
.legend-item { display:flex; align-items:center; gap:4px; }
.legend-dot { width:12px; height:12px; border-radius:3px; }
/* Modal */
.kaldik-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; }
.kaldik-modal-overlay.show { display:flex; }
.kaldik-modal { background:#fff; border-radius:8px; width:100%; max-width:480px; padding:24px; position:relative; }
.kaldik-modal h5 { margin:0 0 16px; font-size:15px; }
.kaldik-modal .close-btn { position:absolute; top:12px; right:16px; cursor:pointer; font-size:18px; color:#666; background:none; border:none; }
.event-list { margin-bottom:12px; }
.event-item { display:flex; align-items:center; gap:8px; padding:6px 8px; border-radius:6px; margin-bottom:4px; font-size:12px; }
.event-item .ev-label { flex:1; }
.event-item .ev-actions { display:flex; gap:4px; }
@media print { .no-print { display:none !important; } }
</style>
@endpush
@section('content')

{{-- Toolbar --}}
<div class="kaldik-toolbar no-print">
    <a href="{{ route('kaldik.index', ['tahun'=>$tahun-1]) }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-chevron-left"></i>
    </a>
    <span class="kaldik-year">{{ $tahun }}</span>
    <a href="{{ route('kaldik.index', ['tahun'=>$tahun+1]) }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-chevron-right"></i>
    </a>
    <a href="{{ route('kaldik.index', ['tahun'=>date('Y')]) }}" class="btn btn-sm btn-outline-secondary">Hari Ini</a>
    <div style="margin-left:auto; display:flex; gap:8px;">
        <button class="btn btn-sm btn-outline-success" onclick="showUploadModal()">
            <i class="fas fa-upload mr-1"></i>Upload Excel
        </button>
        <a href="{{ route('kaldik.template') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-download mr-1"></i>Template
        </a>
        <button class="btn btn-sm btn-outline-dark" onclick="window.print()">
            <i class="fas fa-print mr-1"></i>Print
        </button>
    </div>
</div>

{{-- Legend --}}
<div class="legend-row no-print">
    <div class="legend-item"><div class="legend-dot" style="background:#e53935;"></div> Libur Nasional</div>
    <div class="legend-item"><div class="legend-dot" style="background:#ff6f00;"></div> Cuti Bersama</div>
    <div class="legend-item"><div class="legend-dot" style="background:#7b1fa2;"></div> Libur Semester</div>
    <div class="legend-item"><div class="legend-dot" style="background:#1565c0;"></div> Kegiatan</div>
    <div class="legend-item"><div class="legend-dot" style="background:#00838f;"></div> Pembelajaran Daring</div>
    <div class="legend-item"><div class="legend-dot" style="background:#424242;"></div> Force Majeure</div>
</div>

{{-- Kalender Grid --}}
<div class="kaldik-grid" id="kaldik-grid">
    {{-- Render 12 bulan --}}
    @php
        $today = date('Y-m-d');
        $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $namaHari  = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    @endphp
    @for($bln = 1; $bln <= 12; $bln++)
    @php
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bln, $tahun);
        $hariPertama = date('w', mktime(0,0,0,$bln,1,$tahun)); // 0=Min
    @endphp
    <div class="kaldik-month">
        <div class="kaldik-month-title">{{ $namaBulan[$bln] }} {{ $tahun }}</div>
        <table class="kaldik-cal">
            <thead>
                <tr>
                    @foreach($namaHari as $h)
                    <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @for($i = 0; $i < $hariPertama; $i++)<td></td>@endfor
                    @for($tgl = 1; $tgl <= $jumlahHari; $tgl++)
                    @php
                        $dateStr   = sprintf('%04d-%02d-%02d', $tahun, $bln, $tgl);
                        $dow       = date('w', mktime(0,0,0,$bln,$tgl,$tahun));
                        $eventsHari = $events[$dateStr] ?? collect();
                        $isLibur   = $eventsHari->whereIn('tipe', ['libur_nasional','cuti_bersama','libur_semester'])->count() > 0;
                        $isKegiatan = !$isLibur && $eventsHari->where('tipe','kegiatan')->count() > 0;
                        $isToday   = $dateStr === $today;
                        $col = ($hariPertama + $tgl - 1) % 7;
                    @endphp
                    <td class="{{ $dow==0 ? 'minggu' : ($dow==6 ? 'sabtu' : '') }} {{ $isLibur ? 'libur' : ($isKegiatan ? 'kegiatan' : '') }} {{ $isToday ? 'today' : '' }}"
                        onclick="showDayModal('{{ $dateStr }}', '{{ $namaBulan[$bln] }} {{ $tgl }}, {{ $tahun }}')"
                        data-tanggal="{{ $dateStr }}">
                        {{ $tgl }}
                        @if($eventsHari->count())
                        <div class="kaldik-dots">
                            @foreach($eventsHari as $ev)
                            <span class="kaldik-dot" style="background:{{ App\Models\Kaldik::warna($ev->tipe) }};"></span>
                            @endforeach
                        </div>
                        @endif
                    </td>
                    @if(($hariPertama + $tgl) % 7 == 0 && $tgl < $jumlahHari)
                    </tr><tr>
                    @endif
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>
    @endfor
</div>

{{-- Modal Hari --}}
<div class="kaldik-modal-overlay" id="modal-hari">
    <div class="kaldik-modal">
        <button class="close-btn" onclick="closeModal('modal-hari')">&times;</button>
        <h5 id="modal-hari-title">Tanggal</h5>
        <div class="event-list" id="modal-event-list"></div>
        <hr>
        <div class="no-print">
            <div class="font-weight-bold mb-2" style="font-size:13px;">Tambah Event</div>
            <input type="hidden" id="form-tanggal">
            <div class="form-group mb-2">
                <input type="text" id="form-judul" class="form-control form-control-sm" placeholder="Judul event">
            </div>
            <div class="form-group mb-2">
                <select id="form-tipe" class="form-control form-control-sm">
                    <option value="libur_nasional">Libur Nasional</option>
                    <option value="cuti_bersama">Cuti Bersama</option>
                    <option value="libur_semester">Libur Semester</option>
                    <option value="kegiatan">Kegiatan</option>
                    <option value="daring">Pembelajaran Daring</option>
                    <option value="force_majeure">Force Majeure</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <input type="text" id="form-keterangan" class="form-control form-control-sm" placeholder="Keterangan (opsional)">
            </div>
            <button class="btn btn-sm btn-primary" onclick="simpanEvent()">
                <i class="fas fa-save mr-1"></i>Simpan
            </button>
        </div>
    </div>
</div>

{{-- Modal Upload --}}
<div class="kaldik-modal-overlay" id="modal-upload">
    <div class="kaldik-modal">
        <button class="close-btn" onclick="closeModal('modal-upload')">&times;</button>
        <h5>Upload Excel Kaldik</h5>
        <p style="font-size:12px; color:#666;">Format kolom: <code>tanggal | judul | tipe | keterangan</code><br>
        Tipe valid: <code>libur_nasional, cuti_bersama, libur_semester, kegiatan</code></p>
        <div class="form-group">
            <input type="file" id="upload-file" class="form-control-file" accept=".xlsx,.xls">
        </div>
        <div id="upload-result" class="mt-2" style="font-size:12px;"></div>
        <button class="btn btn-sm btn-success mt-2" onclick="doUpload()">
            <i class="fas fa-upload mr-1"></i>Upload
        </button>
    </div>
</div>

@endsection
@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
const TAHUN = {{ $tahun }};

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

function showUploadModal() {
    document.getElementById('modal-upload').classList.add('show');
}

let currentTanggal = null;
let currentEvents  = {};

function showDayModal(tanggal, label) {
    currentTanggal = tanggal;
    document.getElementById('modal-hari-title').textContent = label;
    document.getElementById('form-tanggal').value = tanggal;
    document.getElementById('form-judul').value = '';
    document.getElementById('form-keterangan').value = '';

    // Load events via API
    fetch(`{{ route('kaldik.api.events') }}?tahun=${tanggal.split('-')[0]}&bulan=${parseInt(tanggal.split('-')[1])}`)
        .then(r => r.json())
        .then(data => {
            const hari = data.filter(e => e.tanggal === tanggal);
            renderEventList(hari);
        });

    document.getElementById('modal-hari').classList.add('show');
}

function renderEventList(events) {
    const list = document.getElementById('modal-event-list');
    if (!events.length) {
        list.innerHTML = '<div class="text-muted" style="font-size:12px;">Belum ada event</div>';
        return;
    }
    list.innerHTML = events.map(e => `
        <div class="event-item" style="background:${e.warna}22; border-left:3px solid ${e.warna};">
            <span class="ev-label"><strong>${e.judul}</strong>${e.keterangan ? ' — '+e.keterangan : ''}</span>
            <div class="ev-actions">
                <button class="btn btn-xs btn-outline-danger" onclick="hapusEvent(${e.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function simpanEvent() {
    const data = {
        tanggal    : document.getElementById('form-tanggal').value,
        judul      : document.getElementById('form-judul').value,
        tipe       : document.getElementById('form-tipe').value,
        keterangan : document.getElementById('form-keterangan').value,
        _token     : CSRF,
    };
    if (!data.judul) { alert('Judul wajib diisi'); return; }

    fetch('{{ route('kaldik.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(() => { location.reload(); });
}

function hapusEvent(id) {
    if (!confirm('Hapus event ini?')) return;
    fetch(`/kaldik/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(() => { location.reload(); });
}

function doUpload() {
    const file = document.getElementById('upload-file').files[0];
    if (!file) { alert('Pilih file terlebih dahulu'); return; }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', CSRF);

    document.getElementById('upload-result').textContent = 'Mengupload...';

    fetch('{{ route('kaldik.upload') }}', {
        method: 'POST',
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('upload-result').innerHTML =
            `<span class="text-success">Berhasil import ${data.inserted} event.</span>` +
            (data.errors.length ? `<br><span class="text-danger">${data.errors.join('<br>')}</span>` : '');
        setTimeout(() => location.reload(), 1500);
    });
}

// Tutup modal klik di luar
document.querySelectorAll('.kaldik-modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
</script>
@endpush