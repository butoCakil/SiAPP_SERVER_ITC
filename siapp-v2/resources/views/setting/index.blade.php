@extends('layouts.app')

@section('title', 'Setting')
@section('page_title', 'Setting Presensi')

@section('content')
<div class="row justify-content-center">
<div class="col-md-11">

    {{-- ── Mode Operasi ── --}}
    <div class="card card-outline {{ $setting->auto_mode ? 'card-success' : 'card-warning' }} mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-robot mr-2"></i>Mode Operasi</h3>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                <div>
                    @if($setting->auto_mode)
                        <span class="badge badge-success p-2" style="font-size:14px;">
                            <i class="fas fa-robot mr-1"></i>OTOMATIS
                        </span>
                        <small class="text-muted ml-2">Presensi dibuka/tutup otomatis sesuai jadwal</small>
                    @else
                        <span class="badge badge-warning p-2" style="font-size:14px;">
                            <i class="fas fa-hand-paper mr-1"></i>MANUAL
                        </span>
                        <small class="text-muted ml-2">Admin kontrol buka/tutup presensi</small>
                    @endif
                </div>
                <form action="{{ route('setting.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mode"        value="{{ $setting->mode }}">
                    <input type="hidden" name="wa"          value="{{ $setting->wa }}">
                    <input type="hidden" name="wta"         value="{{ $setting->wta }}">
                    <input type="hidden" name="wtp"         value="{{ $setting->wtp }}">
                    <input type="hidden" name="wtp_jumat"   value="{{ $setting->wtp_jumat }}">
                    <input type="hidden" name="wp"          value="{{ $setting->wp }}">
                    <input type="hidden" name="wp_jumat"    value="{{ $setting->wp_jumat }}">
                    <input type="hidden" name="hari_kerja"  value="{{ $setting->hari_kerja }}">
                    <input type="hidden" name="waktumasuk"  value="{{ $setting->waktumasuk }}">
                    <input type="hidden" name="waktupulang" value="{{ $setting->waktupulang }}">
                    <input type="hidden" name="info"        value="{{ $setting->info }}">
                    <input type="hidden" name="auto_mode"   value="{{ $setting->auto_mode ? 0 : 1 }}">
                    <button type="submit" class="btn {{ $setting->auto_mode ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas {{ $setting->auto_mode ? 'fa-hand-paper' : 'fa-robot' }} mr-1"></i>
                        Ganti ke {{ $setting->auto_mode ? 'MANUAL' : 'OTOMATIS' }}
                    </button>
                </form>
            </div>

            @if(!$setting->auto_mode)
            <hr>
            <div class="row text-center mt-2">
                @foreach([0 => ['TUTUP','ban','secondary'], 1 => ['MASUK','door-open','success'], 2 => ['PULANG','door-closed','warning']] as $val => $opt)
                <div class="col-md-4 mb-2">
                    <form action="{{ route('setting.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="auto_mode"   value="0">
                        <input type="hidden" name="wa"          value="{{ $setting->wa }}">
                        <input type="hidden" name="wta"         value="{{ $setting->wta }}">
                        <input type="hidden" name="wtp"         value="{{ $setting->wtp }}">
                        <input type="hidden" name="wtp_jumat"   value="{{ $setting->wtp_jumat }}">
                        <input type="hidden" name="wp"          value="{{ $setting->wp }}">
                        <input type="hidden" name="wp_jumat"    value="{{ $setting->wp_jumat }}">
                        <input type="hidden" name="hari_kerja"  value="{{ $setting->hari_kerja }}">
                        <input type="hidden" name="waktumasuk"  value="{{ $setting->waktumasuk }}">
                        <input type="hidden" name="waktupulang" value="{{ $setting->waktupulang }}">
                        <input type="hidden" name="info"        value="{{ $setting->info }}">
                        <button type="submit" name="mode" value="{{ $val }}"
                            class="btn btn-block p-3 {{ $setting->mode == $val ? 'btn-'.$opt[2] : 'btn-outline-'.$opt[2] }}">
                            <i class="fas fa-{{ $opt[1] }} fa-2x mb-1 d-block"></i>
                            <strong>{{ $opt[0] }}</strong>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Jadwal & Waktu ── --}}
    <form action="{{ route('setting.update') }}" method="POST">
        @csrf
        <input type="hidden" name="mode"      value="{{ $setting->mode }}">
        <input type="hidden" name="auto_mode" value="{{ $setting->auto_mode }}">
        <input type="hidden" name="info"      value="{{ $setting->info ?? '' }}">

        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Jadwal & Waktu</h3>
            </div>
            <div class="card-body">

                {{-- Hari Kerja & Tingkat Aktif --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label><i class="fas fa-calendar-week mr-1"></i>Hari Kerja</label>
                        <div class="d-flex" style="gap:20px;">
                            <div class="icheck-success">
                                <input type="radio" name="hari_kerja" id="hk5" value="5"
                                    {{ $setting->hari_kerja == 5 ? 'checked' : '' }}>
                                <label for="hk5">5 Hari (Senin - Jumat)</label>
                            </div>
                            <div class="icheck-primary">
                                <input type="radio" name="hari_kerja" id="hk6" value="6"
                                    {{ $setting->hari_kerja == 6 ? 'checked' : '' }}>
                                <label for="hk6">6 Hari (Senin - Sabtu)</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label><i class="fas fa-school mr-1"></i>Tingkat Aktif</label>
                        <div class="d-flex" style="gap:16px;">
                            @foreach(['X','XI','XII'] as $t)
                            <div class="icheck-success">
                                <input type="checkbox" name="tingkat_aktif[]"
                                    id="tingkat-{{ $t }}" value="{{ $t }}"
                                    {{ in_array($t, json_decode($setting->tingkat_aktif ?? '["X","XI","XII"]', true)) ? 'checked' : '' }}>
                                <label for="tingkat-{{ $t }}">Tingkat {{ $t }}</label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Tingkat yang tidak dicentang disembunyikan dari rekap</small>
                    </div>
                </div>

                {{-- Jadwal --}}
                <div class="row">
                    {{-- Hari Biasa --}}
                    <div class="col-md-6">
                        <div class="card mb-0">
                            <div class="card-header bg-primary text-white py-2">
                                <i class="fas fa-sun mr-1"></i>Hari Biasa (Senin - Kamis{{ $setting->hari_kerja == 6 ? ', Sabtu' : '' }})
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label style="font-size:12px;">Jam Masuk Resmi</label>
                                            <input type="time" name="waktumasuk" class="form-control form-control-sm" value="{{ $setting->waktumasuk }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label style="font-size:12px;">Batas Tepat Waktu <small class="text-muted">(dibuka)</small></label>
                                            <input type="time" name="wa" class="form-control form-control-sm" value="{{ $setting->wa }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label style="font-size:12px;">Batas Toleransi <small class="text-muted">(ditutup)</small></label>
                                            <input type="time" name="wta" class="form-control form-control-sm" value="{{ $setting->wta }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label style="font-size:12px;">Jam Pulang Resmi</label>
                                            <input type="time" name="waktupulang" class="form-control form-control-sm" value="{{ $setting->waktupulang }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-0">
                                            <label style="font-size:12px;">Batas Pulang Awal <small class="text-muted">(dibuka)</small></label>
                                            <input type="time" name="wtp" class="form-control form-control-sm" value="{{ $setting->wtp }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-0">
                                            <label style="font-size:12px;">Batas Akhir Pulang <small class="text-muted">(ditutup)</small></label>
                                            <input type="time" name="wp" class="form-control form-control-sm" value="{{ $setting->wp }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Hari Jumat --}}
                    <div class="col-md-6">
                        <div class="card mb-0">
                            <div class="card-header bg-success text-white py-2">
                                <i class="fas fa-mosque mr-1"></i>Hari Jumat
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info p-2 mb-3" style="font-size:12px;">
                                    <i class="fas fa-info-circle mr-1"></i>Jam masuk Jumat sama dengan hari biasa
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group mb-0">
                                            <label style="font-size:12px;">Batas Pulang Awal <small class="text-muted">(dibuka)</small></label>
                                            <input type="time" name="wtp_jumat" class="form-control form-control-sm" value="{{ $setting->wtp_jumat }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-0">
                                            <label style="font-size:12px;">Batas Akhir Pulang <small class="text-muted">(ditutup)</small></label>
                                            <input type="time" name="wp_jumat" class="form-control form-control-sm" value="{{ $setting->wp_jumat }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Integrasi TIM IT ── --}}
        <div class="card card-outline card-info mb-3">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Integrasi TIM IT</h3>
                <div class="card-tools">
                    @php $sudahDiatur = $setting->timid_presensi_url || $setting->timid_sholat_url; @endphp
                    @if($sudahDiatur)
                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Terhubung</span>
                    @else
                        <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Belum dikonfigurasi</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:12px;">🔵 URL Presensi Masuk/Pulang</label>
                            <input type="text" name="timid_presensi_url" class="form-control form-control-sm"
                                placeholder="https://script.google.com/macros/s/.../exec"
                                value="{{ $setting->timid_presensi_url }}">
                        </div>
                        <div class="form-group">
                            <label style="font-size:12px;">🟠 URL Presensi Sholat</label>
                            <input type="text" name="timid_sholat_url" class="form-control form-control-sm"
                                placeholder="https://script.google.com/macros/s/.../exec"
                                value="{{ $setting->timid_sholat_url }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:12px;">🌸 URL Izin Menstruasi</label>
                            <input type="text" name="timid_izin_mens_url" class="form-control form-control-sm"
                                placeholder="https://script.google.com/macros/s/.../exec"
                                value="{{ $setting->timid_izin_mens_url }}">
                        </div>
                        <div class="form-group">
                            <label style="font-size:12px;">🚪 URL Izin Keluar/Pulang</label>
                            <input type="text" name="timid_ijin_url" class="form-control form-control-sm"
                                placeholder="https://script.google.com/macros/s/.../exec"
                                value="{{ $setting->timid_ijin_url }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label style="font-size:12px;"><i class="fas fa-key mr-1"></i>API Key TIM IT</label>
                            <input type="text" name="timid_api_key" class="form-control form-control-sm"
                                style="font-family:monospace;"
                                placeholder="Token dari TIM IT..."
                                value="{{ $setting->timid_api_key }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label style="font-size:12px;"><i class="fas fa-sync mr-1"></i>Interval Push</label>
                            <div class="d-flex align-items-center" style="gap:8px;">
                                <select name="push_interval" class="form-control form-control-sm">
                                    @foreach([1,2,3,5,10,15,30,60] as $iv)
                                        <option value="{{ $iv }}" {{ (int)($setting->push_interval ?? 5) === $iv ? 'selected' : '' }}>
                                            {{ $iv }} menit
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label style="font-size:12px;"><i class="fas fa-database mr-1"></i>Retensi Log</label>
                            <select name="log_retention" class="form-control form-control-sm">
                                @foreach([7,14,30,60,90] as $days)
                                    <option value="{{ $days }}" {{ ($setting->log_retention ?? 30) == $days ? 'selected' : '' }}>
                                        {{ $days }} hari
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Link REST API ── --}}
        @php
            $simToken = DB::table('api')->where('jenis','sim_token')->where('status','aktif')->value('kode_api');
            $baseUrl  = request()->getSchemeAndHttpHost();
        @endphp
        @if($simToken)
        <div class="card card-outline card-secondary mb-3">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-link mr-2"></i>Link REST API</h3>
                <div class="card-tools">
                    <small class="text-muted">Gunakan header <code>X-Api-Key</code> atau tambahkan <code>?api_key=TOKEN</code></small>
                </div>
            </div>
            <div class="card-body p-2">
                @foreach([
                    ['🔵','Presensi',   '/api/sim/presensi?tanggal='.date('Y-m-d')],
                    ['🟠','Sholat',     '/api/sim/sholat?tanggal='.date('Y-m-d')],
                    ['🌸','Izin Mens',  '/api/sim/izin-mens?tanggal='.date('Y-m-d')],
                    ['🚪','Izin Keluar','/api/sim/ijin?tanggal='.date('Y-m-d')],
                    ['👥','Siswa',      '/api/sim/siswa'],
                ] as [$icon, $label, $path])
                @php $url = $baseUrl . $path . (str_contains($path,'?') ? '&' : '?') . 'api_key=' . $simToken; @endphp
                <div class="d-flex align-items-center mb-1" style="gap:6px;">
                    <span style="width:100px; font-size:11px; font-weight:600; flex-shrink:0;">{{ $icon }} {{ $label }}</span>
                    <input type="text" class="form-control form-control-sm"
                        style="font-family:monospace; font-size:11px;"
                        value="{{ $url }}" readonly
                        id="url-{{ Str::slug($label) }}">
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0"
                        onclick="copyUrl('url-{{ Str::slug($label) }}')" title="Copy">
                        <i class="fas fa-copy"></i>
                    </button>
                    <a href="{{ $url }}" target="_blank"
                        class="btn btn-sm btn-outline-primary flex-shrink-0" title="Buka">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                @endforeach
                <small class="text-muted ml-1">Token dapat diganti di menu <a href="{{ route('apikey') }}">API Key</a></small>
            </div>
        </div>
        @endif

        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-save mr-2"></i>Simpan Setting
        </button>
    </form>

</div>
</div>
@endsection

@push('scripts')
<script>
function copyUrl(id) {
    const el = document.getElementById(id);
    el.select();
    navigator.clipboard.writeText(el.value).then(() => {
        toastr.success('URL berhasil disalin!');
    }).catch(() => {
        document.execCommand('copy');
        toastr.success('URL berhasil disalin!');
    });
}
</script>
@endpush