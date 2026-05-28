@extends('layouts.app')

@section('title', 'Rekap Semester')
@section('page_title', 'Rekap Semester Presensi')

@push('styles')
<style>
.tab-rekap { display:flex; gap:4px; margin-bottom:16px; }
.tab-rekap a {
    padding: 7px 18px;
    border-radius: 6px 6px 0 0;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #dee2e6;
    border-bottom: none;
    background: #f8f9fa;
    color: #495057;
    text-decoration: none;
}
.tab-rekap a.active { background: #007bff; color: #fff; border-color: #007bff; }

.tbl-semester { min-width: 900px; }
.tbl-semester th { font-size: 11px; white-space: nowrap; text-align: center; vertical-align: middle; }
.tbl-semester td { font-size: 11px; vertical-align: middle; }
.tbl-semester td.nama-col { min-width: 160px; font-weight: 600; }
.tbl-semester td.kelas-col { white-space: nowrap; }

.cell-bulan {
    min-width: 90px;
    padding: 4px 6px !important;
    text-align: center;
}
.cell-bulan a {
    display: block;
    text-decoration: none;
    color: inherit;
    border-radius: 6px;
    padding: 4px;
    transition: background .15s;
}
.cell-bulan a:hover { background: #e9f0ff; }

.row-b1 { display:flex; justify-content:center; gap:3px; flex-wrap:wrap; margin-bottom:2px; }
.row-b2 { display:flex; justify-content:center; gap:3px; flex-wrap:wrap; }
.cb {
    font-size: 9px;
    padding: 1px 4px;
    border-radius: 3px;
    color: #fff;
    font-weight: 600;
}
.cb-masuk     { background:#28a745; }
.cb-telat     { background:#ffc107; color:#333 !important; }
.cb-pulang    { background:#007bff; }
.cb-izin      { background:#6c757d; }
.cb-dhuha     { background:#4caf50; }
.cb-dhuhur    { background:#17a2b8; }
.cb-ashar     { background:#6f42c1; }
.cb-mens      { background:#e83e8c; }
.cb-zero      { color:#adb5bd !important; background:none; font-weight:400; }

@media print {
    .no-print { display: none !important; }
    #print-area-semester, #print-area-semester * { visibility: visible; }
    #print-area-semester { position: absolute; left: 0; top: 0; width: 100%; }
    .print-header { display: block !important; }
}
.print-header { display: none; }
</style>
@endpush

@section('content')

{{-- Tab --}}
<div class="tab-rekap">
    <a href="{{ route('presensi.rekap', ['kelas'=>$filterKelas]) }}">Bulanan</a>
    <a href="{{ route('presensi.rekap.semester', ['semester'=>$semester,'tahun'=>$tahun,'kelas'=>$filterKelas]) }}"
       class="active">Semester</a>
</div>

{{-- Filter --}}
<div class="card">
    <div class="card-body py-2">
        <form action="{{ route('presensi.rekap.semester') }}" method="GET"
              class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <select name="semester" class="form-control" style="width:160px;" onchange="this.form.submit()">
                <option value="gasal"  {{ $semester==='gasal'  ? 'selected' : '' }}>Semester Gasal (Jul–Des)</option>
                <option value="genap"  {{ $semester==='genap'  ? 'selected' : '' }}>Semester Genap (Jan–Jun)</option>
            </select>
            <select name="tahun" class="form-control" style="width:100px;" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="kelas" class="form-control" style="width:160px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k }}" {{ $filterKelas==$k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
            @if($filterKelas)
                <a href="{{ route('presensi.rekap.semester', ['semester'=>$semester,'tahun'=>$tahun]) }}"
                   class="btn btn-outline-secondary btn-sm">Reset</a>
            @endif
            <span class="text-muted ml-2" style="font-size:12px;">
                {{ $siswaData->count() }} siswa
            </span>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm ml-auto no-print" style="border-radius:20px;">
                <i class="fas fa-print mr-1"></i>Print PDF
            </button>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card" id="print-area-semester">
    <div class="print-header" style="padding:12px 16px 0;">
        <h5 style="margin:0;">Rekap Semester Presensi & Sholat</h5>
        <div style="font-size:12px; color:#555;">
            Semester {{ ucfirst($semester) }} {{ $tahun }}
            @if($filterKelas) — Kelas {{ $filterKelas }} @endif
            — {{ $siswaData->count() }} siswa
        </div>
        <hr style="margin:8px 0;">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover tbl-semester mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th class="nama-col">Nama</th>
                        <th>Kelas</th>
                        @foreach($periodeList as $bulan)
                            <th class="cell-bulan">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->locale('id')->translatedFormat('M Y') }}
                            </th>
                        @endforeach
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaData as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="nama-col">
                            <a href="{{ route('presensi.rekap.semester.detail', ['nis'=>$s->nis, 'semester'=>$semester, 'tahun'=>$tahun]) }}" style="color:inherit;">
                                {{ $s->nama }}
                            </a>
                        </td>
                        <td class="kelas-col">{{ $s->kelas }}</td>
                        @foreach($periodeList as $bulan)
                            @php $d = $s->bulanData[$bulan]; @endphp
                            <td class="cell-bulan">
                                <a href="{{ route('presensi.rekap.detail', ['nis'=>$s->nis, 'bulan'=>$bulan]) }}">
                                    <div class="row-b1">
                                        <span class="cb {{ $d['masuk']     ? 'cb-masuk'  : 'cb-zero' }}">{{ $d['masuk']     ?: '—' }}</span>
                                        <span class="cb {{ $d['terlambat'] ? 'cb-telat'  : 'cb-zero' }}">{{ $d['terlambat'] ?: '—' }}</span>
                                        <span class="cb {{ $d['pulang']    ? 'cb-pulang' : 'cb-zero' }}">{{ $d['pulang']    ?: '—' }}</span>
                                        <span class="cb {{ $d['izin']      ? 'cb-izin'   : 'cb-zero' }}">{{ $d['izin']      ?: '—' }}</span>
                                    </div>
                                    <div class="row-b2">
                                        <span class="cb {{ $d['dhuha']    ? 'cb-dhuha'  : 'cb-zero' }}">{{ $d['dhuha']    ?: '—' }}</span>
                                        <span class="cb {{ $d['dhuhur']   ? 'cb-dhuhur' : 'cb-zero' }}">{{ $d['dhuhur']   ?: '—' }}</span>
                                        <span class="cb {{ $d['ashar']    ? 'cb-ashar'  : 'cb-zero' }}">{{ $d['ashar']    ?: '—' }}</span>
                                        <span class="cb {{ $d['izinMens'] ? 'cb-mens'   : 'cb-zero' }}">{{ $d['izinMens'] ?: '—' }}</span>
                                    </div>
                                </a>
                            </td>
                        @endforeach
                        <td>
                            <a href="{{ route('presensi.rekap.semester.detail', ['nis'=>$s->nis, 'semester'=>$semester, 'tahun'=>$tahun]) }}"
                               class="btn btn-xs btn-info" title="Detail Semester">
                                <i class="fas fa-th-large"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($periodeList) + 4 }}" class="text-center text-muted py-4">
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
document.addEventListener('DOMContentLoaded', function() {
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control form-control-sm';
    input.placeholder = '🔍 Cari nama siswa...';
    input.style = 'width:200px;';
    document.querySelector('.card .card-body.py-2 form').appendChild(input);
    input.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.tbl-semester tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    });
});
</script>
@endpush