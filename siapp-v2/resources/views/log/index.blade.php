@extends('layouts.app')
@section('title', 'Log Management')
@section('page_title', 'Log Management')

@push('styles')
<style>
.log-table th { font-size: 10px; white-space: nowrap; }
.log-table td { font-size: 11px; vertical-align: middle; font-family: monospace; }
.payload-cell { max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.info-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 700; }
.stat-log { background: #1a2235; border-radius: 10px; padding: 12px 18px; color:#fff; }
.stat-log .val { font-size: 22px; font-weight: 700; }
.stat-log .lbl { font-size: 11px; opacity: 0.6; }
.danger-zone { border: 1px solid rgba(244,67,54,0.3); border-radius: 10px; padding: 14px; background: rgba(244,67,54,0.04); }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('error') }}
</div>
@endif

{{-- Stat Cards --}}
<div class="row mb-3">
    <div class="col-md-3 col-6 mb-2">
        <div class="stat-log">
            <div class="val text-warning">{{ number_format($tempreqTotal) }}</div>
            <div class="lbl">Total Request Log</div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="stat-log">
            <div class="val text-info">{{ number_format($deviceTotal) }}</div>
            <div class="lbl">Total Device Log</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $tab==='tempreq' ? 'active' : '' }}"
            href="{{ route('log', ['tab'=>'tempreq']) }}">
            <i class="fas fa-list mr-1"></i>Request Log
            <span class="badge badge-warning ml-1">{{ number_format($tempreqTotal) }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab==='device' ? 'active' : '' }}"
            href="{{ route('log', ['tab'=>'device']) }}">
            <i class="fas fa-mobile-alt mr-1"></i>Device Log
            <span class="badge badge-info ml-1">{{ number_format($deviceTotal) }}</span>
        </a>
    </li>
</ul>

@if($tab === 'tempreq')
{{-- ── REQUEST LOG ── --}}
<div class="row">

    {{-- Filter --}}
    <div class="col-12 mb-3">
        <form action="{{ route('log') }}" method="GET" class="d-flex flex-wrap" style="gap:8px;">
            <input type="hidden" name="tab" value="tempreq">
            <input type="date" name="tanggal" class="form-control form-control-sm"
                style="width:160px;" value="{{ $filterTanggal }}">
            <input type="text" name="ip" class="form-control form-control-sm"
                style="width:140px;" placeholder="Filter IP..." value="{{ $filterIp }}">
            <select name="info" class="form-control form-control-sm" style="width:120px;">
                <option value="">Semua Jenis</option>
                @foreach($infoList as $info)
                    <option value="{{ $info }}" {{ $filterInfo===$info ? 'selected':'' }}>{{ $info }}</option>
                @endforeach
            </select>
            <input type="text" name="q" class="form-control form-control-sm"
                style="width:200px;" placeholder="🔍 Cari detail..." value="{{ $filterSearch }}">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('log', ['tab'=>'tempreq']) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="col-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title" style="font-size:12px;">
                    <i class="fas fa-list mr-1"></i>Request Log
                    <span class="badge badge-warning ml-1">{{ $tempreqLogs->total() }} hasil</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                    <table class="table table-sm table-hover log-table mb-0">
                        <thead class="thead-dark" style="position:sticky;top:0;z-index:2;">
                            <tr>
                                <th>Timestamp</th>
                                <th>IP</th>
                                <th>Jenis</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tempreqLogs as $log)
                            <tr>
                                <td style="white-space:nowrap;">{{ $log->timestamp }}</td>
                                <td>{{ $log->ip }}</td>
                                <td>
                                    <span class="info-badge" style="
                                        background: {{ $log->info === 'SMPM' ? 'rgba(0,200,83,0.15)' : ($log->info === 'IDTT' ? 'rgba(244,67,54,0.15)' : 'rgba(59,130,246,0.15)') }};
                                        color: {{ $log->info === 'SMPM' ? '#00c853' : ($log->info === 'IDTT' ? '#f44336' : '#3b82f6') }};">
                                        {{ $log->info }}
                                    </span>
                                </td>
                                <td class="payload-cell" title="{{ $log->detail }}">{{ $log->detail }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $tempreqLogs->links() }}
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="col-12 mt-3">
        <div class="danger-zone">
            <h6 class="text-danger mb-3"><i class="fas fa-trash mr-1"></i>Kelola Request Log</h6>
            <div class="d-flex flex-wrap" style="gap:8px;">
                <form action="{{ route('log.tempreq.clear') }}" method="POST"
                    onsubmit="return confirm('Hapus log tanggal {{ $filterTanggal }}?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="tanggal" value="{{ $filterTanggal }}">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-calendar-day mr-1"></i>Hapus Tgl {{ $filterTanggal }}
                    </button>
                </form>
                <form action="{{ route('log.tempreq.clear') }}" method="POST"
                    onsubmit="return confirm('Hapus request log lebih dari 7 hari?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="keep_days" value="7">
                    <button type="submit" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-history mr-1"></i>Hapus &gt; 7 Hari
                    </button>
                </form>
                <form action="{{ route('log.tempreq.clear') }}" method="POST"
                    onsubmit="return confirm('Hapus request log lebih dari 30 hari?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="keep_days" value="30">
                    <button type="submit" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-history mr-1"></i>Hapus &gt; 30 Hari
                    </button>
                </form>
                <form action="{{ route('log.tempreq.clear') }}" method="POST"
                    onsubmit="return confirm('HAPUS SEMUA request log? Tidak bisa dibatalkan!')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="all" value="1">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Hapus Semua ({{ number_format($tempreqTotal) }})
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@else
{{-- ── DEVICE LOG ── --}}
<div class="row">

    {{-- Filter --}}
    <div class="col-12 mb-3">
        <form action="{{ route('log') }}" method="GET" class="d-flex flex-wrap" style="gap:8px;">
            <input type="hidden" name="tab" value="device">
            <input type="date" name="tanggal2" class="form-control form-control-sm"
                style="width:160px;" value="{{ $filterTanggal2 }}">
            <select name="device" class="form-control form-control-sm" style="width:160px;">
                <option value="">Semua Device</option>
                @foreach($deviceList as $d)
                    <option value="{{ $d }}" {{ $filterDevice===$d ? 'selected':'' }}>{{ $d }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('log', ['tab'=>'device']) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title" style="font-size:12px;">
                    <i class="fas fa-mobile-alt mr-1"></i>Device Log
                    <span class="badge badge-info ml-1">{{ $deviceLogs->total() }} hasil</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                    <table class="table table-sm table-hover log-table mb-0">
                        <thead class="thead-dark" style="position:sticky;top:0;z-index:2;">
                            <tr>
                                <th>Waktu</th>
                                <th>Device ID</th>
                                <th>Topic</th>
                                <th>Status</th>
                                <th>RAM</th>
                                <th>RSSI</th>
                                <th>Payload</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deviceLogs as $log)
                            @php
                                $payload = json_decode($log->payload, true);
                            @endphp
                            <tr>
                                <td style="white-space:nowrap;">{{ $log->received_at }}</td>
                                <td><strong>{{ $log->device_id }}</strong></td>
                                <td><small class="text-muted">{{ $log->topic }}</small></td>
                                <td>
                                    @if(isset($payload['status']))
                                        <span class="badge {{ $payload['status']==='online' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $payload['status'] }}
                                        </span>
                                    @else — @endif
                                </td>
                                <td>{{ isset($payload['ram']) ? $payload['ram'].'KB' : '-' }}</td>
                                <td>{{ isset($payload['rssi']) ? $payload['rssi'].'dBm' : '-' }}</td>
                                <td class="payload-cell" title="{{ $log->payload }}">
                                    <small>{{ Str::limit($log->payload, 80) }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $deviceLogs->links() }}
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="col-12 mt-3">
        <div class="danger-zone">
            <h6 class="text-danger mb-3"><i class="fas fa-trash mr-1"></i>Hapus Device Log</h6>
            <div class="d-flex flex-wrap" style="gap:8px;">
                @if($filterDevice)
                <form action="{{ route('log.device.clear') }}" method="POST"
                    onsubmit="return confirm('Hapus semua log device {{ $filterDevice }}?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="device" value="{{ $filterDevice }}">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash mr-1"></i>Hapus Log Device {{ $filterDevice }}
                    </button>
                </form>
                @endif
                <form action="{{ route('log.device.clear') }}" method="POST"
                    onsubmit="return confirm('Hapus log tanggal {{ $filterTanggal2 }}?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="tanggal" value="{{ $filterTanggal2 }}">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-calendar-day mr-1"></i>Hapus Tgl {{ $filterTanggal2 }}
                    </button>
                </form>
                <form action="{{ route('log.device.clear') }}" method="POST"
                    onsubmit="return confirm('Hapus device log lebih dari 7 hari?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="keep_days" value="7">
                    <button type="submit" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-history mr-1"></i>Hapus &gt; 7 Hari
                    </button>
                </form>
                <form action="{{ route('log.device.clear') }}" method="POST"
                    onsubmit="return confirm('Hapus device log lebih dari 30 hari?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="keep_days" value="30">
                    <button type="submit" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-history mr-1"></i>Hapus &gt; 30 Hari
                    </button>
                </form>
                <form action="{{ route('log.device.clear') }}" method="POST"
                    onsubmit="return confirm('HAPUS SEMUA device log? Tidak bisa dibatalkan!')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="all" value="1">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Hapus Semua ({{ number_format($deviceTotal) }})
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endif

@endsection