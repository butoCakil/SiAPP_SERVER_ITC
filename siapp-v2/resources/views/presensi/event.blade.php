@extends('layouts.app')

@section('title', 'Pembiasaan Sholat')
@section('page_title', 'Rekap Pembiasaan Sholat')

@push('styles')
<style>@media print {
    body * { visibility: hidden; }
    #print-area-event, #print-area-event * { visibility: visible; }
    #print-area-event { position: absolute; left: 0; top: 0; width: 100%; }
    @page { size: landscape; margin: 10mm; }
}</style>
<style>
.sholat-card {
    border-radius: 12px;
    padding: 14px 18px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.13);
    margin-bottom: 16px;
}
.sholat-card .s-icon { font-size: 1.8em; }
.sholat-card .s-val  { font-size: 1.7em; font-weight: 700; line-height: 1; }
.sholat-card .s-lbl  { font-size: 11px; opacity: 0.85; }
.sc-dzuhur   { background: linear-gradient(135deg,#ff8800,#cc5500); }
.sc-ashar    { background: linear-gradient(135deg,#9c27b0,#6a0080); }
.sc-keduanya { background: linear-gradient(135deg,#00c853,#00964b); }
.sc-izin     { background: linear-gradient(135deg,#e91e8c,#ad1457); }
.sc-kurang   { background: linear-gradient(135deg,#607d8b,#37474f); }

/* Badge sholat */
.badge-dzuhur     { background:#ff8800; color:#fff; font-size:11px; }
.badge-ashar      { background:#9c27b0; color:#fff; font-size:11px; }
.badge-izin-mens  { background:#e91e8c; color:#fff; font-size:11px; }
.badge-absen      { background:#e0e0e0; color:#999; font-size:11px; }

/* Filter tabs */
.filter-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px; }
.filter-tab {
    padding: 4px 12px;
    border-radius: 20px;
    border: 2px solid #ddd;
    background: #fff;
    font-size: 12px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}
.filter-tab.active, .filter-tab:hover { border-color: #007bff; color: #007bff; }
.filter-tab.active { background: #007bff; color: #fff; }
</style>
</div>
@endpush

@section('content')

{{-- Filter Tanggal --}}
<div class="d-flex align-items-center mb-3 flex-wrap" style="gap:8px;">
    <form action="{{ route('presensi.event') }}" method="GET"
        class="d-flex align-items-center flex-wrap" style="gap:8px;">
        <div class="input-group" style="width:auto;">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
        </div>
        <select name="kelas" class="form-control" style="width:160px;" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k }}" {{ $filterKelas==$k ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-1"></i>Tampilkan
        </button>
        <a href="{{ route('presensi.event') }}" class="btn btn-outline-secondary">
            <i class="fas fa-calendar-day mr-1"></i>Hari Ini
        </a>
    </form>
    <span class="badge badge-light border ml-auto" style="font-size:13px;">
        📅 {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
    </span>
</div>

{{-- Stat Cards --}}
<div class="row">
    <div class="col-6 col-md col-sm-6">
        <div class="sholat-card sc-dzuhur">
            <div class="s-icon">🕛</div>
            <div>
                <div class="s-val">{{ $totalDzuhur }}</div>
                <div class="s-lbl">Dzuhur</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md col-sm-6">
        <div class="sholat-card sc-ashar">
            <div class="s-icon">🕓</div>
            <div>
                <div class="s-val">{{ $totalAshar }}</div>
                <div class="s-lbl">Ashar</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md col-sm-6">
        <div class="sholat-card sc-keduanya">
            <div class="s-icon">✅</div>
            <div>
                <div class="s-val">{{ $totalKeduanya }}</div>
                <div class="s-lbl">Keduanya</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md col-sm-6">
        <div class="sholat-card sc-izin">
            <div class="s-icon">🌸</div>
            <div>
                <div class="s-val">{{ $totalIzin }}</div>
                <div class="s-lbl">Izin Mens</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md col-sm-6">
        <div class="sholat-card sc-kurang">
            <div class="s-icon">⚠️</div>
            <div>
                <div class="s-val">{{ $totalTidakKeduanya }}</div>
                <div class="s-lbl">Belum Lengkap</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="filter-tabs">
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm mr-2" style="border-radius:20px;">
        <i class="fas fa-print mr-1"></i>Print PDF
    </button>
    <button class="filter-tab active" onclick="filterTabel('semua', this)">🕌 Semua ({{ $siswaList->count() }})</button>
    <button class="filter-tab" onclick="filterTabel('dzuhur', this)">🕛 Dzuhur saja ({{ $totalDzuhur - $totalKeduanya }})</button>
    <button class="filter-tab" onclick="filterTabel('ashar', this)">🕓 Ashar saja ({{ $totalAshar - $totalKeduanya }})</button>
    <button class="filter-tab" onclick="filterTabel('keduanya', this)">✅ Keduanya ({{ $totalKeduanya }})</button>
    <button class="filter-tab" onclick="filterTabel('izin', this)">🌸 Izin Mens ({{ $totalIzin }})</button>
    <button class="filter-tab" onclick="filterTabel('belum', this)">⚠️ Belum Lengkap ({{ $totalTidakKeduanya }})</button>
</div>

{{-- Tabel --}}
<div id="print-area-event">
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-mosque mr-2"></i>Data Pembiasaan Sholat
            <span class="badge badge-warning ml-2" id="badge-count">{{ $siswaList->count() }} siswa</span>
        </h3>
        <div class="card-tools">
            <input type="text" id="searchInput" class="form-control form-control-sm"
                placeholder="🔍 Cari nama/NIS/kelas..." style="width:200px;">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0" id="tabelEvent">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Dzuhur</th>
                        <th>Ashar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $i => $s)
                    @php
                        $hasDzuhur  = !is_null($s['dzuhur']);
                        $hasAshar   = !is_null($s['ashar']);
                        $hasKeduanya = $hasDzuhur && $hasAshar;
                        $hasIzin    = $s['dzuhur_izin'] || $s['ashar_izin'];
                        $belum      = !$hasKeduanya;

                        $rowFilter = 'semua ';
                        if ($hasDzuhur && !$hasAshar) $rowFilter .= 'dzuhur ';
                        if ($hasAshar && !$hasDzuhur) $rowFilter .= 'ashar ';
                        if ($hasKeduanya) $rowFilter .= 'keduanya ';
                        if ($hasIzin) $rowFilter .= 'izin ';
                        if ($belum) $rowFilter .= 'belum ';
                    @endphp
                    <tr data-filter="{{ trim($rowFilter) }}"
                        data-search="{{ strtolower($s['nis'].' '.$s['nama'].' '.$s['kelas']) }}">
                        <td>{{ $i + 1 }}</td>
                        <td><code>{{ $s['nis'] }}</code></td>
                        <td><strong>{{ $s['nama'] }}</strong></td>
                        <td><small>{{ $s['kelas'] }}</small></td>
                        <td>
                            @if($hasDzuhur)
                                @if($s['dzuhur_izin'])
                                    <span class="badge badge-izin-mens">🌸 Izin {{ $s['dzuhur'] }}</span>
                                @else
                                    <span class="badge badge-dzuhur">🕛 {{ $s['dzuhur'] }}</span>
                                @endif
                            @else
                                <span class="badge badge-absen">—</span>
                            @endif
                        </td>
                        <td>
                            @if($hasAshar)
                                @if($s['ashar_izin'])
                                    <span class="badge badge-izin-mens">🌸 Izin {{ $s['ashar'] }}</span>
                                @else
                                    <span class="badge badge-ashar">🕓 {{ $s['ashar'] }}</span>
                                @endif
                            @else
                                <span class="badge badge-absen">—</span>
                            @endif
                        </td>
                        <td>
                            @if($hasIzin)
                                <span class="badge badge-izin-mens">🌸 Izin Mens</span>
                            @elseif($hasKeduanya)
                                <span class="badge badge-success">✅ Lengkap</span>
                            @elseif($hasDzuhur || $hasAshar)
                                <span class="badge badge-warning text-dark">⚠️ Sebagian</span>
                            @else
                                <span class="badge badge-absen">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-mosque fa-2x mb-2 d-block"></i>
                            Tidak ada data pembiasaan untuk tanggal ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted" style="font-size:11px;">
        Dzuhur: {{ $totalDzuhur }} | Ashar: {{ $totalAshar }} |
        Keduanya: {{ $totalKeduanya }} | Izin Mens: {{ $totalIzin }} |
        Belum Lengkap: {{ $totalTidakKeduanya }}
    </div>
</div>

@endsection

@push('scripts')
<script>
let activeFilter = 'semua';

function filterTabel(type, btn) {
    activeFilter = type;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    applyFilter();
}

function applyFilter() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    let count = 0;
    document.querySelectorAll('#tabelEvent tbody tr[data-filter]').forEach(row => {
        const matchFilter = activeFilter === 'semua' || row.dataset.filter.includes(activeFilter);
        const matchSearch = !search || row.dataset.search.includes(search);
        const show = matchFilter && matchSearch;
        row.style.display = show ? '' : 'none';
        if (show) count++;
    });
    document.getElementById('badge-count').textContent = count + ' siswa';
}

document.getElementById('searchInput').addEventListener('input', applyFilter);
</script>
</div>
@endpush