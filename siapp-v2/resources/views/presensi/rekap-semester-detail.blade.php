@extends('layouts.app')

@section('title', 'Detail Semester - ' . $siswa->nama)
@section('page_title', 'Detail Rekap Semester')

@push('styles')
<style>
.tab-rekap { display:flex; gap:4px; margin-bottom:16px; }
.tab-rekap a {
    padding: 7px 18px; border-radius: 6px 6px 0 0;
    font-size: 13px; font-weight: 600;
    border: 1px solid #dee2e6; border-bottom: none;
    background: #f8f9fa; color: #495057; text-decoration: none;
}
.tab-rekap a.active { background: #007bff; color: #fff; border-color: #007bff; }

.kalender-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}
.kal-header {
    text-align: center; font-weight: 700; font-size: 10px;
    padding: 4px 0; background: #343a40; color: #fff; border-radius: 4px;
}
.kal-hari {
    border-radius: 6px; padding: 4px 3px; min-height: 60px;
    font-size: 9px; border: 1px solid #dee2e6;
}
.kal-hari .tgl-num { font-size: 12px; font-weight: 700; display: block; margin-bottom: 1px; }
.kal-hari.hadir     { background: #d4edda; border-color: #28a745; }
.kal-hari.terlambat { background: #fff3cd; border-color: #ffc107; }
.kal-hari.tanpa_ket { background: #f8d7da; border-color: #dc3545; }
.kal-hari.libur     { background: #e9ecef; border-color: #adb5bd; color: #6c757d; }
.kal-hari.kosong    { background: transparent; border: none; }
.kal-hari.today     { box-shadow: 0 0 0 2px #007bff; }
.kal-badge {
    display: inline-block; font-size: 7px; padding: 1px 2px;
    border-radius: 2px; color: #fff; margin-top: 1px; line-height: 1.3;
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
    border-radius: 8px; padding: 10px 14px; color: #fff;
    text-align: center; margin-bottom: 10px;
}
.sum-card .val { font-size: 1.6em; font-weight: 700; line-height: 1; }
.sum-card .lbl { font-size: 10px; opacity: .85; }

.bulan-section { margin-bottom: 24px; }
.bulan-title {
    font-weight: 700; font-size: 13px; margin-bottom: 8px;
    padding: 6px 10px; background: #343a40; color: #fff; border-radius: 6px;
}
</style>
@endpush

@section('content')

{{-- Tab --}}
<div class="tab-rekap">
    <a href="{{ route('presensi.rekap.detail', ['nis'=>$nis, 'bulan'=>$periodeList[0]]) }}">Bulanan</a>
    <a href="#" class="active">Semester</a>
</div>

{{-- Navigasi --}}
<div class="card">
    <div class="card-body py-2 d-flex align-items-center flex-wrap" style="gap:8px;">
        <a href="{{ route('presensi.rekap.semester', ['semester'=>$semester,'tahun'=>$tahun]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <strong class="ml-2">{{ $siswa->nama }}</strong>
        <span class="badge badge-info">{{ $siswa->kelas }}</span>
        <span class="text-muted" style="font-size:12px;">NIS: {{ $siswa->nis }}</span>
        <div class="ml-auto d-flex align-items-center" style="gap:6px;">
            <a href="{{ route('presensi.rekap.semester.detail', ['nis'=>$nis,'semester'=>$semester,'tahun'=>$tahunSebelum]) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-left"></i> {{ $tahunSebelum }}
            </a>
            <select class="form-control form-control-sm" style="width:130px;"
                    onchange="window.location='{{ route('presensi.rekap.semester.detail', $nis) }}?semester='+document.getElementById('selSmt').value+'&tahun={{ $tahun }}'">
                <option value="gasal" {{ $semester==='gasal' ? 'selected' : '' }}>Gasal (Jul–Des)</option>
                <option value="genap" {{ $semester==='genap' ? 'selected' : '' }}>Genap (Jan–Jun)</option>
            </select>
            <span class="font-weight-bold">{{ $tahun }}</span>
            @if($tahunBerikut <= date('Y'))
                <a href="{{ route('presensi.rekap.semester.detail', ['nis'=>$nis,'semester'=>$semester,'tahun'=>$tahunBerikut]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    {{ $tahunBerikut }} <i class="fas fa-chevron-right"></i>
                </a>
            @endif
            <button onclick="window.print()" class="btn btn-sm btn-outline-dark">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>
</div>

{{-- Summary Total Semester --}}
<div class="row">
    @php
    $items = [
        ['val'=>$summaryTotal['masuk'],     'lbl'=>'Masuk',      'bg'=>'linear-gradient(135deg,#28a745,#1e7e34)'],
        ['val'=>$summaryTotal['terlambat'], 'lbl'=>'Terlambat',  'bg'=>'linear-gradient(135deg,#ffc107,#e0a800)','dark'=>true],
        ['val'=>$summaryTotal['pulang'],    'lbl'=>'Pulang',     'bg'=>'linear-gradient(135deg,#007bff,#0056b3)'],
        ['val'=>$summaryTotal['izin'],      'lbl'=>'Izin Keluar','bg'=>'linear-gradient(135deg,#6c757d,#495057)'],
        ['val'=>$summaryTotal['dhuha'],     'lbl'=>'Dhuha',      'bg'=>'linear-gradient(135deg,#4caf50,#2e7d32)'],
        ['val'=>$summaryTotal['dhuhur'],    'lbl'=>'Dhuhur',     'bg'=>'linear-gradient(135deg,#17a2b8,#117a8b)'],
        ['val'=>$summaryTotal['ashar'],     'lbl'=>'Ashar',      'bg'=>'linear-gradient(135deg,#6f42c1,#4e2d8a)'],
        ['val'=>$summaryTotal['izin_mens'], 'lbl'=>'Izin Mens',  'bg'=>'linear-gradient(135deg,#e83e8c,#c0266a)'],
        ['val'=>$summaryTotal['libur'],     'lbl'=>'Hari Libur', 'bg'=>'linear-gradient(135deg,#adb5bd,#6c757d)'],
    ];
    @endphp
    @foreach($items as $item)
    <div class="col-6 col-md-3">
        <div class="sum-card" style="background:{{ $item['bg'] }};{{ isset($item['dark']) ? 'color:#333;' : '' }}">
            <div class="val">{{ $item['val'] }}</div>
            <div class="lbl">{{ $item['lbl'] }} <small>(Semester)</small></div>
        </div>
    </div>
    @endforeach
</div>

{{-- 6 Kalender --}}
<div class="row">
    @foreach($kalenderPerBulan as $bulan => $data)
    <div class="col-12 col-md-6">
        <div class="card bulan-section">
            <div class="card-body p-2">
                <div class="bulan-title d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-calendar-alt mr-1"></i>
                        {{ \Carbon\Carbon::createFromDate($data['thn'], $data['bln'], 1)->locale('id')->translatedFormat('F Y') }}
                    </span>
                    <a href="{{ route('presensi.rekap.detail', ['nis'=>$nis,'bulan'=>$bulan]) }}"
                       class="btn btn-xs btn-light" title="Detail bulan ini">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="kalender-grid">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $h)
                        <div class="kal-header">{{ $h }}</div>
                    @endforeach

                    @php
                        $firstDay = date('w', strtotime($data['thn'].'-'.sprintf('%02d',$data['bln']).'-01'));
                    @endphp
                    @for($pad = 0; $pad < $firstDay; $pad++)
                        <div class="kal-hari kosong"></div>
                    @endfor

                    @foreach($data['kalender'] as $k)
                    @php $p = $k['presensi']; $isToday = $k['tgl'] === date('Y-m-d'); @endphp
                    <div class="kal-hari {{ $k['tipe'] }} {{ $isToday ? 'today' : '' }}">
                        <span class="tgl-num">{{ $k['hari'] }}</span>
                        @if($k['tipe'] === 'libur')
                            <span class="kal-badge bg-libur" title="{{ $k['kaldik_judul'] ?? 'Libur' }}">Lib</span>
                        @elseif($p)
                            @if(in_array($p->ketmasuk, ['T','TL','TLT']))
                                <span class="kal-badge bg-terlambat">Tel</span>
                            @else
                                <span class="kal-badge bg-masuk">Msk</span>
                            @endif
                        @else
                            <span class="kal-badge bg-tanpa_ket">—</span>
                        @endif
                        @if($k['dhuha'] && $k['dhuha']->ruang !== 'Izin Mens')
                            <span class="kal-badge bg-dhuha">Dh</span>
                        @endif
                        @if($k['dhuhur'] && $k['dhuhur']->ruang !== 'Izin Mens')
                            <span class="kal-badge bg-dhuhur">Dz</span>
                        @endif
                        @if($k['ashar'] && $k['ashar']->ruang !== 'Izin Mens')
                            <span class="kal-badge bg-ashar">As</span>
                        @endif
                        @if($k['izin_mens'])
                            <span class="kal-badge bg-mens">IM</span>
                        @endif
                        @if($k['izins']->count() > 0)
                            <span class="kal-badge bg-izin">Iz</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection