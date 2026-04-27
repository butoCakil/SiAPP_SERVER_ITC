@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<style>
.small-box { border-radius: 10px; }
.stat-sholat {
    border-radius: 10px;
    padding: 14px 16px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.12);
}
.stat-sholat .s-icon { font-size: 1.6em; }
.stat-sholat .s-val  { font-size: 1.5em; font-weight: 700; line-height: 1; }
.stat-sholat .s-lbl  { font-size: 11px; opacity: 0.85; }
.sc-dzuhur { background: linear-gradient(135deg,#ff8800,#cc5500); }
.sc-ashar  { background: linear-gradient(135deg,#9c27b0,#6a0080); }
.sc-izin   { background: linear-gradient(135deg,#e91e8c,#ad1457); }

.status-card {
    border-radius: 10px;
    padding: 14px 18px;
    text-align: center;
}

.jam-display {
    font-size: 2.2em;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    color: #333;
    letter-spacing: 2px;
}
.tgl-display { font-size: 12px; color: #888; }

.badge-masuk  { background:#00c853; color:#fff; }
.badge-tl     { background:#ff8800; color:#fff; }
.badge-tlt    { background:#ff3b3b; color:#fff; }
.badge-sholat-d { background:#ff8800; color:#fff; }
.badge-sholat-a { background:#9c27b0; color:#fff; }
.badge-izin   { background:#e91e8c; color:#fff; }

.recent-table td { font-size: 11px; vertical-align: middle; }
.recent-table th { font-size: 11px; }

.tipe-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
}
.dot-masuk  { background: #00c853; }
.dot-sholat { background: #ff8800; }
</style>
@endpush

@section('content')

{{-- Row 1: Stat Cards Presensi --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ $totalHadir }}</h3><p>Hadir Hari Ini</p></div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
            <a href="{{ route('presensi') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ $totalTepat }}</h3><p>Tepat Waktu</p></div>
            <div class="icon"><i class="fas fa-clock"></i></div>
            <a href="{{ route('presensi') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3>{{ $totalTelat }}</h3><p>Terlambat</p></div>
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

{{-- Row 2: Sholat Cards --}}
<div class="row">
    <div class="col-md-4 col-6 mb-3">
        <div class="stat-sholat sc-dzuhur">
            <div class="s-icon">🕛</div>
            <div>
                <div class="s-val">{{ $totalDzuhur }}</div>
                <div class="s-lbl">Sholat Dzuhur</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6 mb-3">
        <div class="stat-sholat sc-ashar">
            <div class="s-icon">🕓</div>
            <div>
                <div class="s-val">{{ $totalAshar }}</div>
                <div class="s-lbl">Sholat Ashar</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6 mb-3">
        <div class="stat-sholat sc-izin">
            <div class="s-icon">🌸</div>
            <div>
                <div class="s-val">{{ $totalIzin }}</div>
                <div class="s-lbl">Izin Mens</div>
            </div>
        </div>
    </div>
</div>

{{-- Row 3: Status + Jam + Recent --}}
<div class="row">

    {{-- Status & Jam --}}
    <div class="col-md-4">

        {{-- Jam --}}
        <div class="card card-outline card-secondary mb-3">
            <div class="card-body text-center py-3">
                <div class="jam-display" id="jam-realtime">{{ date('H:i:s') }}</div>
                <div class="tgl-display">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>

        {{-- Status Presensi Masuk/Pulang --}}
        <div class="card card-outline card-{{ $statusMasuk['color'] }} mb-3">
            <div class="card-header py-2">
                <h3 class="card-title" style="font-size:12px;">
                    <i class="fas fa-user-clock mr-1"></i>Presensi Masuk/Pulang
                </h3>
                <div class="card-tools">
                    <small class="text-muted">{{ $setting->auto_mode ? '🤖 Auto' : '✋ Manual' }}</small>
                </div>
            </div>
            <div class="card-body text-center py-3">
                <span class="badge badge-{{ $statusMasuk['color'] }} p-2" style="font-size:14px;">
                    <i class="fas fa-{{ $statusMasuk['icon'] }} mr-1"></i>
                    {{ $statusMasuk['label'] }}
                </span>
                <div class="mt-2" style="font-size:11px; color:#888;">
                    <i class="fas fa-sign-in-alt mr-1"></i>{{ $setting->waktumasuk ?? '-' }}
                    &nbsp;|&nbsp;
                    <i class="fas fa-sign-out-alt mr-1"></i>{{ $setting->waktupulang ?? '-' }}
                </div>
                <a href="{{ route('setting') }}" class="btn btn-xs btn-outline-secondary mt-2" style="font-size:11px;">
                    <i class="fas fa-cog mr-1"></i>Setting
                </a>
            </div>
        </div>

        {{-- Status Sholat --}}
        <div class="card card-outline card-{{ $statusSholat['color'] === 'purple' ? 'secondary' : $statusSholat['color'] }} mb-3">
            <div class="card-header py-2">
                <h3 class="card-title" style="font-size:12px;">
                    <i class="fas fa-mosque mr-1"></i>Presensi Sholat
                </h3>
            </div>
            <div class="card-body text-center py-3">
                <span class="badge p-2" style="font-size:14px;
                    background: {{ $statusSholat['color'] === 'warning' ? '#ff8800' : ($statusSholat['color'] === 'purple' ? '#9c27b0' : '#9e9e9e') }};
                    color:#fff;">
                    <i class="fas fa-mosque mr-1"></i>
                    {{ $statusSholat['label'] }}
                </span>
                <div class="mt-2" style="font-size:11px; color:#888;">
                    🕛 Dzuhur: 11:45 - 14:30
                    &nbsp;|&nbsp;
                    🕓 Ashar: 14:30 - 17:00
                </div>
                <a href="{{ route('presensi.event') }}" class="btn btn-xs btn-outline-secondary mt-2" style="font-size:11px;">
                    <i class="fas fa-list mr-1"></i>Rekap
                </a>
            </div>
        </div>

    </div>

    {{-- Recent Presensi --}}
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>Aktivitas Terbaru
                    <span class="badge badge-primary ml-2">{{ $tanggal }}</span>
                </h3>
                <div class="card-tools">
                    <span class="text-muted" style="font-size:11px;">
                        <span class="tipe-dot dot-masuk"></span>Presensi
                        &nbsp;
                        <span class="tipe-dot dot-sholat"></span>Sholat
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover recent-table mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Tipe</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Waktu</th>
                                <th>Ket</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody id="recent-tbody">
                            @forelse($recentAll as $r)
                            <tr>
                                <td>
                                    @if($r['tipe'] === 'masuk')
                                        <span class="tipe-dot dot-masuk"></span>
                                        <small>Masuk</small>
                                    @else
                                        <span class="tipe-dot dot-sholat"></span>
                                        <small>Sholat</small>
                                    @endif
                                </td>
                                <td><strong>{{ $r['nama'] }}</strong></td>
                                <td><small>{{ $r['info'] }}</small></td>
                                <td>{{ $r['waktu'] }}</td>
                                <td>
                                    @if($r['tipe'] === 'masuk')
                                        @if($r['ket'] === 'TW')
                                            <span class="badge badge-masuk">Tepat</span>
                                        @elseif(in_array($r['ket'], ['TL','TLT']))
                                            <span class="badge badge-tl">Telat</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $r['ket'] ?? '-' }}</span>
                                        @endif
                                    @else
                                        @if($r['device'] === 'Izin Mens')
                                            <span class="badge badge-izin">🌸 Izin</span>
                                        @elseif($r['ket'] === 'DZUHUR')
                                            <span class="badge badge-sholat-d">🕛 Dzuhur</span>
                                        @else
                                            <span class="badge badge-sholat-a">🕓 Ashar</span>
                                        @endif
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $r['device'] ?? '-' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-inbox mr-2"></i>Belum ada aktivitas hari ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('presensi') }}" class="btn btn-sm btn-outline-primary mr-2">
                    <i class="fas fa-list mr-1"></i>Semua Presensi
                </a>
                <a href="{{ route('presensi.event') }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-mosque mr-1"></i>Rekap Sholat
                </a>
            </div>
        </div>
    </div>

</div>

{{-- Rekap Sholat Per Kelas --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-mosque mr-2"></i>Rekap Pembiasaan Sholat Per Kelas
                    <span class="badge badge-primary ml-2">
                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                    </span>
                </h3>
                <div class="card-tools" style="font-size:11px;">
                    <span style="background:#00c853;color:#fff;padding:2px 8px;border-radius:4px;">■ D+A</span>
                    <span style="background:#00b0ff;color:#fff;padding:2px 8px;border-radius:4px;margin-left:4px;">■ Dzuhur</span>
                    <span style="background:#9c27b0;color:#fff;padding:2px 8px;border-radius:4px;margin-left:4px;">■ Ashar</span>
                    <span style="background:#e91e8c;color:#fff;padding:2px 8px;border-radius:4px;margin-left:4px;">■ Izin Mens</span>
                    <span style="background:#f44336;color:#fff;padding:2px 8px;border-radius:4px;margin-left:4px;">■ Alpa</span>
                </div>
            </div>
            <div class="card-body p-2">
                @foreach($rekapKelas as $r)
                @php
                    $keduanya  = min($r->dzuhur, $r->ashar);
                    $hanyaD    = $r->dzuhur - $keduanya;
                    $hanyaA    = $r->ashar  - $keduanya;
                    $alpa      = max(0, $r->total - $r->dzuhur - $hanyaA - $r->izin);

                    // Lebar bar (persen dari total)
                    $wKeduanya = $r->total > 0 ? round($keduanya / $r->total * 100, 1) : 0;
                    $wHanyaD   = $r->total > 0 ? round($hanyaD   / $r->total * 100, 1) : 0;
                    $wHanyaA   = $r->total > 0 ? round($hanyaA   / $r->total * 100, 1) : 0;
                    $wIzin     = $r->total > 0 ? round($r->izin  / $r->total * 100, 1) : 0;
                    $wAlpa     = $r->total > 0 ? round($alpa     / $r->total * 100, 1) : 0;
                @endphp
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">

                    {{-- Label kelas --}}
                    <div style="width:80px; font-size:11px; font-weight:700; flex-shrink:0; text-align:right; color:#333;">
                        {{ $r->kelas }}
                    </div>

                    {{-- Horizontal stacked bar --}}
                    <div style="flex:1; height:22px; border-radius:6px; overflow:hidden; background:#eee; display:flex; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                        @if($wKeduanya > 0)
                        <div style="width:{{ $wKeduanya }}%; background:linear-gradient(90deg,#00c853,#69f0ae);
                            display:flex; align-items:center; justify-content:center;
                            font-size:10px; font-weight:700; color:#fff; overflow:hidden; white-space:nowrap;">
                            {{ $keduanya > 0 ? $keduanya : '' }}
                        </div>
                        @endif
                        @if($wHanyaD > 0)
                        <div style="width:{{ $wHanyaD }}%; background:linear-gradient(90deg,#00b0ff,#40c4ff);
                            display:flex; align-items:center; justify-content:center;
                            font-size:10px; font-weight:700; color:#fff; overflow:hidden; white-space:nowrap;">
                            {{ $hanyaD > 0 ? $hanyaD : '' }}
                        </div>
                        @endif
                        @if($wHanyaA > 0)
                        <div style="width:{{ $wHanyaA }}%; background:linear-gradient(90deg,#9c27b0,#ce93d8);
                            display:flex; align-items:center; justify-content:center;
                            font-size:10px; font-weight:700; color:#fff; overflow:hidden; white-space:nowrap;">
                            {{ $hanyaA > 0 ? $hanyaA : '' }}
                        </div>
                        @endif
                        @if($wIzin > 0)
                        <div style="width:{{ $wIzin }}%; background:linear-gradient(90deg,#e91e8c,#f48fb1);
                            display:flex; align-items:center; justify-content:center;
                            font-size:10px; font-weight:700; color:#fff; overflow:hidden; white-space:nowrap;">
                            {{ $r->izin > 0 ? $r->izin : '' }}
                        </div>
                        @endif
                        @if($wAlpa > 0)
                        <div style="width:{{ $wAlpa }}%; background:linear-gradient(90deg,#f44336,#ef9a9a);
                            display:flex; align-items:center; justify-content:center;
                            font-size:10px; font-weight:700; color:#fff; overflow:hidden; white-space:nowrap;">
                            {{ $alpa > 0 ? $alpa : '' }}
                        </div>
                        @endif
                    </div>

                    {{-- Badge ringkasan --}}
                    <div style="display:flex; gap:3px; flex-shrink:0; font-size:10px;">
                        <span style="background:#00c853;color:#fff;border-radius:4px;padding:1px 5px;min-width:22px;text-align:center;" title="Keduanya">{{ $keduanya }}</span>
                        <span style="background:#00b0ff;color:#fff;border-radius:4px;padding:1px 5px;min-width:22px;text-align:center;" title="Dzuhur">{{ $r->dzuhur }}</span>
                        <span style="background:#9c27b0;color:#fff;border-radius:4px;padding:1px 5px;min-width:22px;text-align:center;" title="Ashar">{{ $r->ashar }}</span>
                        <span style="background:#e91e8c;color:#fff;border-radius:4px;padding:1px 5px;min-width:22px;text-align:center;" title="Izin Mens">{{ $r->izin }}</span>
                        <span style="background:#f44336;color:#fff;border-radius:4px;padding:1px 5px;min-width:22px;text-align:center;" title="Alpa">{{ $alpa }}</span>
                        <span style="background:#1565c0;color:#fff;border-radius:4px;padding:1px 5px;min-width:22px;text-align:center;" title="Total">{{ $r->total }}</span>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Chart Pembiasaan Sholat --}}
<div class="row mt-2">
    <div class="col-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>Rekap Pembiasaan Sholat
                    <small class="text-muted ml-2">14 hari terakhir</small>
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm" id="chart-toggle">
                        <button class="btn btn-warning active" onclick="setChartType('bar', this)">Bar</button>
                        <button class="btn btn-outline-warning" onclick="setChartType('line', this)">Line</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="chartSholat" height="80"></canvas>
            </div>
            <div class="card-footer text-muted" style="font-size:11px;">
                🕛 Dzuhur &nbsp;|&nbsp; 🕓 Ashar &nbsp;|&nbsp; 🌸 Izin Mens
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Jam realtime ──
function updateJam() {
    const now = new Date();
    const hh  = String(now.getHours()).padStart(2,'0');
    const mm  = String(now.getMinutes()).padStart(2,'0');
    const ss  = String(now.getSeconds()).padStart(2,'0');
    const el  = document.getElementById('jam-realtime');
    if (el) el.textContent = `${hh}:${mm}:${ss}`;
}
setInterval(updateJam, 1000);
updateJam();

// ── Auto refresh halaman tiap 60 detik ──
setTimeout(() => location.reload(), 60000);

// ── Chart Pembiasaan Sholat ──
const chartData = @json($chartSholat);
const labels    = chartData.map(d => d.tanggal);
const dzuhur    = chartData.map(d => parseInt(d.dzuhur));
const ashar     = chartData.map(d => parseInt(d.ashar));
const izin      = chartData.map(d => parseInt(d.izin));

const ctx = document.getElementById('chartSholat').getContext('2d');
let myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Dzuhur',
                data: dzuhur,
                backgroundColor: 'rgba(255,136,0,0.75)',
                borderColor: '#ff8800',
                borderWidth: 2,
                borderRadius: 4,
            },
            {
                label: 'Ashar',
                data: ashar,
                backgroundColor: 'rgba(156,39,176,0.75)',
                borderColor: '#9c27b0',
                borderWidth: 2,
                borderRadius: 4,
            },
            {
                label: 'Izin Mens',
                data: izin,
                backgroundColor: 'rgba(233,30,140,0.6)',
                borderColor: '#e91e8c',
                borderWidth: 2,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    title: ctx => '📅 ' + ctx[0].label,
                    label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' siswa'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { stepSize: 50 }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

function setChartType(type, btn) {
    myChart.config.type = type;
    myChart.update();
    document.querySelectorAll('#chart-toggle button').forEach(b => {
        b.classList.remove('active', 'btn-warning');
        b.classList.add('btn-outline-warning');
    });
    btn.classList.add('active', 'btn-warning');
    btn.classList.remove('btn-outline-warning');
}
</script>
@endpush