@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    {{-- Stat Cards --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalHadir }}</h3>
                <p>Hadir Hari Ini</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
            <a href="{{ route('presensi') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalTepat }}</h3>
                <p>Tepat Waktu</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
            <a href="{{ route('presensi') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalTelat }}</h3>
                <p>Terlambat</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
            <a href="{{ route('presensi') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $deviceOnline }}<small>/{{ $totalDevice }}</small></h3>
                <p>Device Online</p>
            </div>
            <div class="icon"><i class="fas fa-mobile-alt"></i></div>
            <a href="{{ route('device') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    {{-- Setting Mode --}}
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cog mr-2"></i>Status Presensi</h3>
            </div>
            <div class="card-body text-center">
                @if($setting)
                    @if($setting->mode == 1)
                        <span class="badge badge-success p-3" style="font-size:16px;">
                            <i class="fas fa-door-open mr-2"></i>MASUK AKTIF
                        </span>
                    @elseif($setting->mode == 2)
                        <span class="badge badge-warning p-3" style="font-size:16px;">
                            <i class="fas fa-door-closed mr-2"></i>PULANG AKTIF
                        </span>
                    @else
                        <span class="badge badge-secondary p-3" style="font-size:16px;">
                            <i class="fas fa-ban mr-2"></i>PRESENSI DITUTUP
                        </span>
                    @endif
                    <div class="mt-3 text-muted small">
                        <i class="fas fa-clock mr-1"></i>Masuk: {{ $setting->waktumasuk }}
                        &nbsp;|&nbsp;
                        <i class="fas fa-clock mr-1"></i>Pulang: {{ $setting->waktupulang }}
                    </div>
                @endif
                <a href="{{ route('setting') }}" class="btn btn-sm btn-outline-primary mt-3">
                    <i class="fas fa-edit mr-1"></i>Ubah Setting
                </a>
            </div>
        </div>
    </div>

    {{-- Presensi Terbaru --}}
    <div class="col-md-8">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>Presensi Terbaru
                    <span class="badge badge-success ml-2">{{ $tanggal }}</span>
                </h3>
                <div class="card-tools">
                    <a href="{{ route('presensi') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-eye"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama</th>
                                <th>Masuk</th>
                                <th>Ket</th>
                                <th>Pulang</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensiTerbaru as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->nama }}</strong><br>
                                    <small class="text-muted">{{ $p->nomorinduk }}</small>
                                </td>
                                <td>{{ $p->waktumasuk ?? '-' }}</td>
                                <td>
                                    @if($p->ketmasuk == 'TW')
                                        <span class="badge badge-success">Tepat</span>
                                    @elseif(in_array($p->ketmasuk, ['TL','TLT']))
                                        <span class="badge badge-warning">Telat</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $p->ketmasuk ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>{{ $p->waktupulang ?? '-' }}</td>
                                <td><small>{{ $p->infodevice2 ?? '-' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <i class="fas fa-inbox mr-2"></i>Belum ada presensi hari ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto refresh setiap 30 detik
setTimeout(() => location.reload(), 30000);
</script>
@endpush