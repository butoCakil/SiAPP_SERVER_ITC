@extends('layouts.app')

@section('title', 'Rekap Bulanan')
@section('page_title', 'Rekap Bulanan Presensi')

@push('styles')
<style>
.tbl-rekap th { font-size: 11px; white-space: nowrap; text-align: center; }
.tbl-rekap td { font-size: 12px; vertical-align: middle; }
.tbl-rekap td.num { text-align: center; font-weight: 600; }
.badge-masuk     { background:#28a745; color:#fff; }
.badge-terlambat { background:#ffc107; color:#333; }
.badge-pulang    { background:#007bff; color:#fff; }
.badge-izin      { background:#6c757d; color:#fff; }
.badge-dhuha     { background:#4caf50; color:#fff; }
.badge-dhuhur    { background:#17a2b8; color:#fff; }
.badge-ashar     { background:#6f42c1; color:#fff; }
.badge-mens      { background:#e83e8c; color:#fff; }
.nav-bulan { display:flex; align-items:center; gap:8px; }

.tab-rekap { display:flex; gap:4px; margin-bottom:16px; }
.tab-rekap a {
    padding: 7px 18px; border-radius: 6px 6px 0 0;
    font-size: 13px; font-weight: 600;
    border: 1px solid #dee2e6; border-bottom: none;
    background: #f8f9fa; color: #495057; text-decoration: none;
}
.tab-rekap a.active { background: #007bff; color: #fff; border-color: #007bff; }
</style>
@endpush

@section('content')

{{-- Tab --}}
<div class="tab-rekap">
    <a href="{{ route('presensi.rekap', ['bulan'=>$bulan,'kelas'=>$filterKelas]) }}" class="active">Bulanan</a>
    <a href="{{ route('presensi.rekap.semester', ['kelas'=>$filterKelas]) }}">Semester</a>
</div>

{{-- Filter & Navigasi --}}
<div class="card no-print">
    <div class="card-body py-2">
        <form action="{{ route('presensi.rekap') }}" method="GET"
              class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <div class="nav-bulan">
                @if(isset($bulanSebelum))
                    <a href="{{ route('presensi.rekap', ['bulan'=>$bulanSebelum,'kelas'=>$filterKelas]) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                <div class="input-group" style="width:auto;">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <input type="month" name="bulan" class="form-control"
                           value="{{ $bulan }}" max="{{ date('Y-m') }}"
                           onchange="this.form.submit()">
                </div>
                @if($bulanBerikut)
                    <a href="{{ route('presensi.rekap', ['bulan'=>$bulanBerikut,'kelas'=>$filterKelas]) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
            </div>
            <select name="kelas" class="form-control" style="width:160px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k }}" {{ $filterKelas==$k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
            @if($filterKelas)
                <a href="{{ route('presensi.rekap', ['bulan'=>$bulan]) }}"
                   class="btn btn-outline-secondary btn-sm">Reset</a>
            @endif
            <span class="text-muted ml-2" style="font-size:12px;">
                {{ \Carbon\Carbon::createFromDate($tahun, $bln, 1)->locale('id')->translatedFormat('F Y') }}
                — {{ $siswaData->count() }} siswa
            </span>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover tbl-rekap mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th title="Hadir Masuk"><i class="fas fa-sign-in-alt"></i> Masuk</th>
                        <th title="Terlambat"><i class="fas fa-clock"></i> Telat</th>
                        <th title="Pulang"><i class="fas fa-sign-out-alt"></i> Pulang</th>
                        <th title="Izin Keluar"><i class="fas fa-door-open"></i> Izin</th>
                        <th title="Sholat Dhuha"><i class="fas fa-sun"></i> Dhuha</th>
                        <th title="Sholat Dhuhur"><i class="fas fa-mosque"></i> Dhuhur</th>
                        <th title="Sholat Ashar"><i class="fas fa-mosque"></i> Ashar</th>
                        <th title="Izin Mens"><i class="fas fa-venus"></i> Mens</th>
                        <th class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaData as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $s->nama }}</strong></td>
                        <td>{{ $s->kelas }}</td>
                        <td class="num">
                            @if($s->masuk > 0)
                                <span class="badge badge-masuk">{{ $s->masuk }}</span>
                            @else — @endif
                        </td>
                        <td class="num">
                            @if($s->terlambat > 0)
                                <span class="badge badge-terlambat">{{ $s->terlambat }}</span>
                            @else — @endif
                        </td>
                        <td class="num">
                            @if($s->pulang > 0)
                                <span class="badge badge-pulang">{{ $s->pulang }}</span>
                            @else — @endif
                        </td>
                        <td class="num">
                            @if($s->izin > 0)
                                <span class="badge badge-izin">{{ $s->izin }}</span>
                            @else — @endif
                        </td>
                        <td class="num">
                            @if($s->dhuha > 0)
                                <span class="badge badge-dhuha">{{ $s->dhuha }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                            </td>
                            <td>
                            @if($s->dhuhur > 0)
                                <span class="badge badge-dhuhur">{{ $s->dhuhur }}</span>
                            @else — @endif
                        </td>
                        <td class="num">
                            @if($s->ashar > 0)
                                <span class="badge badge-ashar">{{ $s->ashar }}</span>
                            @else — @endif
                        </td>
                        <td class="num">
                            @if($s->izinMens > 0)
                                <span class="badge badge-mens">{{ $s->izinMens }}</span>
                            @else — @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('presensi.rekap.detail', ['nis'=>$s->nis, 'bulan'=>$bulan]) }}"
                               class="btn btn-xs btn-primary" title="Detail Kalender">
                                <i class="fas fa-calendar-alt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            Tidak ada data siswa
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Search realtime
document.addEventListener('DOMContentLoaded', function() {
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control form-control-sm';
    input.placeholder = '🔍 Cari nama siswa...';
    input.style = 'width:200px;';
    document.querySelector('.card-body.py-2 form').appendChild(input);

    input.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.tbl-rekap tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    });
});
</script>
@endpush