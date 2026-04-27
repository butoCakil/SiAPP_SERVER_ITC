@extends('layouts.app')

@section('title', 'Presensi Harian')
@section('page_title', 'Presensi Harian')

@push('styles')
<style>
.stat-card {
    border-radius: 12px;
    padding: 16px 20px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.13);
    margin-bottom: 16px;
}
.stat-card .stat-icon { font-size: 2em; opacity: 0.85; }
.stat-card .stat-val  { font-size: 1.8em; font-weight: 700; line-height: 1; }
.stat-card .stat-lbl  { font-size: 12px; opacity: 0.85; }
.sc-total  { background: linear-gradient(135deg,#007bff,#0056b3); }
.sc-tepat  { background: linear-gradient(135deg,#00c853,#00964b); }
.sc-telat  { background: linear-gradient(135deg,#ff8800,#cc6600); }
.sc-pulang { background: linear-gradient(135deg,#9c27b0,#6a0080); }

.filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.table-presensi th { font-size: 12px; white-space: nowrap; }
.table-presensi td { font-size: 12px; vertical-align: middle; }

.badge-tw  { background:#00c853; color:#fff; }
.badge-tl  { background:#ff8800; color:#fff; }
.badge-tlt { background:#ff3b3b; color:#fff; }
.badge-plg { background:#007bff; color:#fff; }
.badge-pa  { background:#ff8800; color:#fff; }
</style>
@endpush

@section('content')

{{-- Filter --}}
<div class="filter-bar">
    <form action="{{ route('presensi') }}" method="GET" class="d-flex gap-2 align-items-center flex-wrap" style="gap:8px;">
        <div class="input-group" style="width:auto;">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-1"></i>Tampilkan
        </button>
        <a href="{{ route('presensi') }}" class="btn btn-outline-secondary">
            <i class="fas fa-calendar-day mr-1"></i>Hari Ini
        </a>
    </form>
    <span class="badge badge-light border ml-auto" style="font-size:13px;">
        📅 {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
    </span>
</div>

{{-- Stat Cards --}}
<div class="row">
    <div class="col-6 col-md-3">
        <div class="stat-card sc-total">
            <div class="stat-icon">👥</div>
            <div>
                <div class="stat-val">{{ $total }}</div>
                <div class="stat-lbl">Total Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card sc-tepat">
            <div class="stat-icon">✅</div>
            <div>
                <div class="stat-val">{{ $tepat }}</div>
                <div class="stat-lbl">Tepat Waktu</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card sc-telat">
            <div class="stat-icon">⏰</div>
            <div>
                <div class="stat-val">{{ $telat }}</div>
                <div class="stat-lbl">Terlambat</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card sc-pulang">
            <div class="stat-icon">🚪</div>
            <div>
                <div class="stat-val">{{ $sudahPulang }}</div>
                <div class="stat-lbl">Sudah Pulang</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>Data Presensi
            <span class="badge badge-primary ml-2">{{ $total }} siswa</span>
        </h3>
        <div class="card-tools">
            <input type="text" id="searchInput" class="form-control form-control-sm"
                placeholder="🔍 Cari nama/NIS..." style="width:180px;">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-presensi mb-0" id="tabelPresensi">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Masuk</th>
                        <th>Ket</th>
                        <th>Selisih</th>
                        <th>Pulang</th>
                        <th>Ket</th>
                        <th>Device</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensi as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><code>{{ $p->nomorinduk }}</code></td>
                        <td><strong>{{ $p->nama }}</strong></td>
                        <td>{{ $p->info }}</td>
                        <td>{{ $p->waktumasuk ?? '-' }}</td>
                        <td>
                            @if($p->ketmasuk === 'TW')
                                <span class="badge badge-tw">Tepat</span>
                            @elseif($p->ketmasuk === 'TL')
                                <span class="badge badge-tl">Toleransi</span>
                            @elseif($p->ketmasuk === 'TLT')
                                <span class="badge badge-tlt">Terlambat</span>
                            @elseif($p->ketmasuk === 'T')
                                <span class="badge badge-tl">Telat</span>
                            @else
                                <span class="badge badge-secondary">{{ $p->ketmasuk ?? '-' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($p->a_time && $p->a_time !== '00:00:00')
                                <small class="text-warning">+{{ $p->a_time }}</small>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : '-' }}</td>
                        <td>
                            @if($p->ketpulang === 'PLG')
                                <span class="badge badge-plg">Normal</span>
                            @elseif($p->ketpulang === 'PA')
                                <span class="badge badge-pa">Awal</span>
                            @else
                                <span class="badge badge-secondary">{{ $p->ketpulang ?? '-' }}</span>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $p->infodevice2 ?? '-' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Tidak ada data presensi untuk tanggal ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($total > 0)
    <div class="card-footer text-muted" style="font-size:11px;">
        Total {{ $total }} data | Tepat: {{ $tepat }} | Terlambat: {{ $telat }} | Pulang: {{ $sudahPulang }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Search filter
document.getElementById('searchInput').addEventListener('input', function() {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#tabelPresensi tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
});
</script>
@endpush