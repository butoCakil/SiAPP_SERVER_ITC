@extends('layouts.app')

@section('title', 'Detail Rekap - ' . $siswa->nama)
@section('page_title', 'Detail Rekap Per Siswa')

@push('styles')
<style>
.kalender-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}
.kal-header {
    text-align: center;
    font-weight: 700;
    font-size: 11px;
    padding: 6px 0;
    background: #343a40;
    color: #fff;
    border-radius: 6px;
}
.kal-hari {
    border-radius: 8px;
    padding: 5px 4px;
    min-height: 80px;
    font-size: 10px;
    border: 1px solid #dee2e6;
}
.kal-hari .tgl-num {
    font-size: 14px;
    font-weight: 700;
    display: block;
    margin-bottom: 2px;
}
.kal-hari.hadir     { background: #d4edda; border-color: #28a745; }
.kal-hari.terlambat { background: #fff3cd; border-color: #ffc107; }
.kal-hari.tanpa_ket { background: #f8d7da; border-color: #dc3545; }
.kal-hari.libur     { background: #e9ecef; border-color: #adb5bd; color: #6c757d; }
.kal-hari.kosong    { background: transparent; border: none; }
.kal-hari.today     { box-shadow: 0 0 0 2px #007bff; }
.kal-badge {
    display: inline-block;
    font-size: 8px;
    padding: 1px 3px;
    border-radius: 3px;
    color: #fff;
    margin-top: 1px;
    line-height: 1.4;
}
.bg-masuk     { background: #28a745; }
.bg-terlambat { background: #ffc107; color: #333 !important; }
.bg-tanpa_ket { background: #dc3545; }
.bg-libur     { background: #adb5bd; }
.bg-dhuha     { background: #4caf50; }
.bg-dhuhur    { background: #17a2b8; }
.bg-ashar     { background: #6f42c1; }
.bg-mens      { background: #e83e8c; }
.bg-izin      { background: #6c757d; }
.sum-card {
    border-radius: 8px;
    padding: 10px 14px;
    color: #fff;
    text-align: center;
    margin-bottom: 10px;
}
.sum-card .val { font-size: 1.8em; font-weight: 700; line-height: 1; }
.sum-card .lbl { font-size: 10px; opacity: .85; }
@media print {
    .no-print { display: none !important; }
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: absolute; left: 0; top: 0; width: 100%; }
}

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
    <a href="{{ route('presensi.rekap.detail', ['nis'=>$siswa->nis,'bulan'=>$bulan]) }}" class="active">Bulanan</a>
    <a href="{{ route('presensi.rekap.semester.detail', ['nis'=>$siswa->nis,'semester'=> date('m') >= 7 ? 'gasal' : 'genap','tahun'=>date('Y')]) }}">Semester</a>
</div>

{{-- Navigasi --}}
<div class="card no-print">
    <div class="card-body py-2 d-flex align-items-center flex-wrap" style="gap:8px;">
        <a href="{{ route('presensi.rekap', ['bulan'=>$bulan]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <strong class="ml-2">{{ $siswa->nama }}</strong>
        <span class="badge badge-info">{{ $siswa->kelas }}</span>
        <span class="text-muted" style="font-size:12px;">NIS: {{ $siswa->nis }}</span>
        <div class="ml-auto d-flex" style="gap:6px;">
            <a href="{{ route('presensi.rekap.detail', ['nis'=>$nis,'bulan'=>$bulanSebelum]) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </a>
            @if($bulanBerikut)
                <a href="{{ route('presensi.rekap.detail', ['nis'=>$nis,'bulan'=>$bulanBerikut]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    Berikutnya <i class="fas fa-chevron-right"></i>
                </a>
            @endif
            <input type="month" class="form-control form-control-sm" style="width:150px;"
                   value="{{ $bulan }}" max="{{ date('Y-m') }}"
                   onchange="window.location='{{ route('presensi.rekap.detail', $nis) }}?bulan='+this.value">
            <button onclick="window.print()" class="btn btn-sm btn-outline-dark">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>
</div>

<div id="print-area">

{{-- Summary --}}
<div class="row">
    @php
    $summaryItems = [
        ['val'=>$summary['masuk'],     'lbl'=>'Masuk',    'bg'=>'linear-gradient(135deg,#28a745,#1e7e34)'],
        ['val'=>$summary['terlambat'], 'lbl'=>'Terlambat','bg'=>'linear-gradient(135deg,#ffc107,#e0a800)', 'dark'=>true],
        ['val'=>$summary['pulang'],    'lbl'=>'Pulang',   'bg'=>'linear-gradient(135deg,#007bff,#0056b3)'],
        ['val'=>$summary['izin'],      'lbl'=>'Izin Keluar','bg'=>'linear-gradient(135deg,#6c757d,#495057)'],
        ['val'=>$summary['dhuha'],      'lbl'=>'Dhuha',    'bg'=>'linear-gradient(135deg,#4caf50,#2e7d32)'],
        ['val'=>$summary['dhuhur'],    'lbl'=>'Dhuhur',   'bg'=>'linear-gradient(135deg,#17a2b8,#117a8b)'],
        ['val'=>$summary['ashar'],     'lbl'=>'Ashar',    'bg'=>'linear-gradient(135deg,#6f42c1,#4e2d8a)'],
        ['val'=>$summary['izin_mens'], 'lbl'=>'Izin Mens','bg'=>'linear-gradient(135deg,#e83e8c,#c0266a)'],
        ['val'=>$summary['libur'],     'lbl'=>'Hari Libur','bg'=>'linear-gradient(135deg,#adb5bd,#6c757d)'],
    ];
    @endphp
    @foreach($summaryItems as $item)
    <div class="col-6 col-md-3">
        <div class="sum-card" style="background:{{ $item['bg'] }};{{ isset($item['dark']) ? 'color:#333;' : '' }}">
            <div class="val">{{ $item['val'] }}</div>
            <div class="lbl">{{ $item['lbl'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Kalender --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt mr-2"></i>
            {{ \Carbon\Carbon::createFromDate($tahun, $bln, 1)->locale('id')->translatedFormat('F Y') }}
            — {{ $siswa->nama }}
        </h3>
    </div>
    <div class="card-body">
        <div class="kalender-grid">
            @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $h)
                <div class="kal-header">{{ $h }}</div>
            @endforeach

            @php
                $firstDay = date('w', strtotime($tahun.'-'.sprintf('%02d',$bln).'-01'));
            @endphp
            @for($pad = 0; $pad < $firstDay; $pad++)
                <div class="kal-hari kosong"></div>
            @endfor

            @foreach($kalender as $k)
            @php $p = $k['presensi']; $isToday = $k['tgl'] === date('Y-m-d'); @endphp
            <div class="kal-hari {{ $k['tipe'] }} {{ $isToday ? 'today' : '' }}">
                <span class="tgl-num">{{ $k['hari'] }}</span>

                @if($k['tipe'] === 'libur')
                    <span class="kal-badge bg-libur">{{ $k['kaldik_judul'] ?? 'Libur' }}</span>
                @elseif($p)
                    <div>{{ $p->waktumasuk }}</div>
                    @if(in_array($p->ketmasuk, ['T','TL','TLT']))
                        <span class="kal-badge bg-terlambat">Telat</span>
                    @else
                        <span class="kal-badge bg-masuk">Masuk</span>
                    @endif
                    @if($p->waktupulang && $p->waktupulang !== '00:00:00')
                        <div>{{ $p->waktupulang }}</div>
                    @endif
                @else
                    <span class="kal-badge bg-tanpa_ket">—</span>
                @endif

                {{-- Sholat --}}
                @if($k['dhuha'] && $k['dhuha']->ruang !== 'Izin Mens')
                    <span class="kal-badge bg-dhuha">Dha</span>
                @endif
                @if($k['dhuhur'] && $k['dhuhur']->ruang !== 'Izin Mens')
                    <span class="kal-badge bg-dhuhur">Dzh</span>
                @endif
                @if($k['ashar'] && $k['ashar']->ruang !== 'Izin Mens')
                    <span class="kal-badge bg-ashar">Ash</span>
                @endif
                @if($k['izin_mens'])
                    <span class="kal-badge bg-mens">IM</span>
                @endif
                @if($k['izins']->count() > 0)
                    <span class="kal-badge bg-izin">Izin</span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Legenda --}}
        <div class="mt-3 d-flex flex-wrap no-print" style="gap:8px;font-size:11px;">
            <span><span class="kal-badge bg-masuk">■</span> Masuk</span>
            <span><span class="kal-badge bg-terlambat">■</span> Terlambat</span>
            <span><span class="kal-badge bg-tanpa_ket">■</span> Tanpa Ket</span>
            <span><span class="kal-badge bg-libur">■</span> Libur</span>
            <span><span class="kal-badge bg-dhuha">■</span> Dhuha</span>
            <span><span class="kal-badge bg-dhuhur">■</span> Dhuhur</span>
            <span><span class="kal-badge bg-ashar">■</span> Ashar</span>
            <span><span class="kal-badge bg-mens">■</span> Izin Mens</span>
            <span><span class="kal-badge bg-izin">■</span> Izin Keluar</span>
        </div>
    </div>
</div>

</div>
@endsection