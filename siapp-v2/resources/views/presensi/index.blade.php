@extends('layouts.app')

@section('title', 'Presensi Harian')
@section('page_title', 'Presensi Harian')

@push('styles')
<style>
.stat-card {
    border-radius: 12px;
    padding: 14px 18px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.13);
    margin-bottom: 16px;
    cursor: pointer;
    transition: transform 0.2s, filter 0.2s;
    text-decoration: none;
}
.stat-card:hover { transform: translateY(-2px); filter: brightness(1.08); color:#fff; }
.stat-card.active-filter { box-shadow: 0 0 0 3px #fff, 0 0 0 5px #333; }
.stat-card .s-icon { font-size: 1.8em; opacity: 0.85; }
.stat-card .s-val  { font-size: 1.7em; font-weight: 700; line-height: 1; }
.stat-card .s-lbl  { font-size: 11px; opacity: 0.85; }
.sc-total  { background: linear-gradient(135deg,#007bff,#0056b3); }
.sc-tepat  { background: linear-gradient(135deg,#00c853,#00964b); }
.sc-telat  { background: linear-gradient(135deg,#ff8800,#cc6600); }
.sc-pulang { background: linear-gradient(135deg,#9c27b0,#6a0080); }

.filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:14px; }

.badge-tw  { background:#00c853; color:#fff; }
.badge-tl  { background:#ff8800; color:#fff; }
.badge-tlt { background:#ff3b3b; color:#fff; }
.badge-plg { background:#007bff; color:#fff; }
.badge-pa  { background:#ff8800; color:#fff; }

.table-presensi th { font-size: 11px; white-space: nowrap; }
.table-presensi td { font-size: 12px; vertical-align: middle; }

/* Print styles */
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
    @page { size: landscape; margin: 10mm; }
    .table-presensi th, .table-presensi td { font-size: 10px; }
    .print-header { display: block !important; }
}
.print-header { display: none; margin-bottom: 10px; }
</style>
@endpush

@section('content')

{{-- Stat Cards (klik untuk filter) --}}
<div class="row no-print">
    <div class="col-6 col-md-3">
        <a href="{{ route('presensi', ['tanggal'=>$tanggal, 'kelas'=>$kelas]) }}"
            class="stat-card sc-total {{ !$filterKet ? 'active-filter' : '' }}">
            <div class="s-icon">👥</div>
            <div><div class="s-val">{{ $total }}</div><div class="s-lbl">Total Hadir</div></div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('presensi', ['tanggal'=>$tanggal, 'kelas'=>$kelas, 'ket'=>'tepat']) }}"
            class="stat-card sc-tepat {{ $filterKet==='tepat' ? 'active-filter' : '' }}">
            <div class="s-icon">✅</div>
            <div><div class="s-val">{{ $tepat }}</div><div class="s-lbl">Tepat Waktu</div></div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('presensi', ['tanggal'=>$tanggal, 'kelas'=>$kelas, 'ket'=>'terlambat']) }}"
            class="stat-card sc-telat {{ $filterKet==='terlambat' ? 'active-filter' : '' }}">
            <div class="s-icon">⏰</div>
            <div><div class="s-val">{{ $telat }}</div><div class="s-lbl">Terlambat</div></div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('presensi', ['tanggal'=>$tanggal, 'kelas'=>$kelas, 'ket'=>'pulang_awal']) }}"
            class="stat-card sc-pulang {{ $filterKet==='pulang_awal' ? 'active-filter' : '' }}">
            <div class="s-icon">🚪</div>
            <div><div class="s-val">{{ $sudahPulang }}</div><div class="s-lbl">Sudah Pulang</div></div>
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar no-print">
    <form action="{{ route('presensi') }}" method="GET"
        class="d-flex align-items-center flex-wrap" style="gap:8px;" id="filter-form">
        <div class="input-group" style="width:auto;">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" name="tanggal" class="form-control"
                value="{{ $tanggal }}" onchange="this.form.submit()">
        </div>
        <select name="kelas" class="form-control" style="width:160px;" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k }}" {{ $kelas==$k ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </select>
        <select name="ket" class="form-control" style="width:160px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="tepat"      {{ $filterKet==='tepat'      ? 'selected' : '' }}>Tepat Waktu</option>
            <option value="terlambat"  {{ $filterKet==='terlambat'  ? 'selected' : '' }}>Terlambat</option>
            <option value="pulang_awal"{{ $filterKet==='pulang_awal'? 'selected' : '' }}>Pulang Awal</option>
        </select>
        <a href="{{ route('presensi') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        <input type="text" id="searchInput" class="form-control" style="width:180px;"
            placeholder="🔍 Cari nama/NIS...">
    </form>
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm ml-auto">
        <i class="fas fa-print mr-1"></i>Print PDF
    </button>
</div>

{{-- Print Area --}}
<div id="print-area">

    {{-- Print header (hanya muncul saat print) --}}
    <div class="print-header">
        <strong>Rekap Presensi Harian</strong> —
        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
        @if($kelas) | Kelas: {{ $kelas }} @endif
        @if($filterKet) | Filter: {{ ucfirst($filterKet) }} @endif
        <br>
        <small>Total: {{ $total }} | Tepat: {{ $tepat }} | Terlambat: {{ $telat }} | Pulang: {{ $sudahPulang }}</small>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header no-print">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>Data Presensi
                <span class="badge badge-primary ml-2">{{ $total }} siswa</span>
                @if($kelas)
                    <span class="badge badge-info ml-1">{{ $kelas }}</span>
                @endif
                @if($filterKet)
                    <span class="badge badge-warning ml-1">{{ ucfirst(str_replace('_',' ',$filterKet)) }}</span>
                @endif
            </h3>
            <div class="card-tools">
                    <a href="{{ route('presensi.create') }}" class="btn btn-sm btn-success no-print">
                        <i class="fas fa-plus mr-1"></i>Tambah Manual
                    </a>
                </div>
            <div class="card-tools">
                <span class="text-muted" style="font-size:11px;">
                    📅 {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered table-presensi mb-0" id="tabelPresensi">
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
                            <th class="no-print">Device</th>
                            <th class="no-print">Aksi</th>
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
                                @elseif(in_array($p->ketmasuk, ['TL','T']))
                                    <span class="badge badge-tl">Toleransi</span>
                                @elseif($p->ketmasuk === 'TLT')
                                    <span class="badge badge-tlt">Terlambat</span>
                                @else
                                    <span class="badge badge-secondary">{{ $p->ketmasuk ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($p->a_time && $p->a_time !== '00:00:00')
                                    <small class="text-warning">+{{ $p->a_time }}</small>
                                @else -
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
                            <td class="no-print"><small class="text-muted">{{ $p->infodevice2 ?? '-' }}</small></td>
                            <td class="no-print">
                                <div class="d-flex" style="gap:4px;">
                                    <a href="{{ route('presensi.edit', $p->id) }}"
                                        class="btn btn-xs btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('presensi.destroy', $p->id) }}"
                                        method="POST" style="display:inline;"
                                        onsubmit="return confirm('Hapus presensi {{ $p->nama }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada data presensi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($total > 0)
        <div class="card-footer" style="font-size:11px;">
            Total {{ $total }} | Tepat: {{ $tepat }} | Terlambat: {{ $telat }} | Pulang: {{ $sudahPulang }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#tabelPresensi tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
});
</script>
@endpush