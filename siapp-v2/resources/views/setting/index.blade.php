@extends('layouts.app')

@section('title', 'Setting')
@section('page_title', 'Setting Presensi')

@push('styles')
<style>
.setting-tabs { display:flex; gap:4px; margin-bottom:0; border-bottom:2px solid #dee2e6; flex-wrap:wrap; }
.setting-tab {
    padding: 8px 18px; border-radius: 6px 6px 0 0;
    font-size: 13px; font-weight: 600; cursor: pointer;
    border: 1px solid transparent; border-bottom: none;
    background: #f8f9fa; color: #495057;
    transition: all 0.15s;
}
.setting-tab:hover { background: #e9ecef; }
.setting-tab.active { background: #fff; color: #007bff; border-color: #dee2e6; border-bottom-color: #fff; margin-bottom: -2px; }
.setting-tab i { margin-right: 5px; }
.tab-pane { display:none; padding-top:16px; }
.tab-pane.active { display:block; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-md-11">

    {{-- Nav Tabs --}}
    <div class="setting-tabs">
        <div class="setting-tab active" onclick="switchTab('operasi', this)">
            <i class="fas fa-robot"></i>Operasi
        </div>
        <div class="setting-tab" onclick="switchTab('jadwal', this)">
            <i class="fas fa-calendar-alt"></i>Jadwal Aktif
        </div>
        <div class="setting-tab" onclick="switchTab('device', this)">
            <i class="fas fa-microchip"></i>Jadwal Device
        </div>
        <div class="setting-tab" onclick="switchTab('sholat', this)">
            <i class="fas fa-mosque"></i>Sholat
        </div>
        <div class="setting-tab" onclick="switchTab('integrasi', this)">
            <i class="fas fa-exchange-alt"></i>Integrasi
        </div>
        <div class="setting-tab" onclick="switchTab('notifikasi', this)">
            <i class="fab fa-whatsapp"></i>Notifikasi
        </div>
        <div class="setting-tab" onclick="switchTab('sinkronisasi', this)">
            <i class="fas fa-sync-alt"></i>Sinkronisasi
        </div>
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- TAB 1: OPERASI --}}
    {{-- ══════════════════════════════════════ --}}
    <div class="tab-pane active" id="tab-operasi">
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
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- FORM UTAMA (Tab 2-6) --}}
    {{-- ══════════════════════════════════════ --}}
    <form action="{{ route('setting.update') }}" method="POST">
        @csrf
        <input type="hidden" name="mode"      value="{{ $setting->mode }}">
        <input type="hidden" name="auto_mode" value="{{ $setting->auto_mode }}">
        <input type="hidden" name="info"      value="{{ $setting->info ?? '' }}">

        {{-- ══════════════════════════════════════ --}}
        {{-- TAB 2: JADWAL AKTIF --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="tab-pane" id="tab-jadwal">
            <div class="card card-outline card-danger mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Jadwal & Waktu Aktif</h3>
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
            <button type="submit" class="btn btn-primary btn-block mb-4">
                <i class="fas fa-save mr-2"></i>Simpan Setting
            </button>
        </div>

        {{-- ══════════════════════════════════════ --}}
        {{-- TAB 3: JADWAL DEVICE --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="tab-pane" id="tab-device">
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title"><i class="fas fa-microchip mr-2"></i>Jadwal Upload & Restart Device</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-upload mr-1"></i>Upload #1</label>
                                <input type="time" name="upload1" class="form-control form-control-sm"
                                    value="{{ $setting->upload1 ?? '07:30:00' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-upload mr-1"></i>Upload #2</label>
                                <input type="time" name="upload2" class="form-control form-control-sm"
                                    value="{{ $setting->upload2 ?? '13:00:00' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-redo mr-1"></i>Restart #1</label>
                                <input type="time" name="restart1" class="form-control form-control-sm"
                                    value="{{ $setting->restart1 ?? '05:00:00' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-redo mr-1"></i>Restart #2</label>
                                <input type="time" name="restart2" class="form-control form-control-sm"
                                    value="{{ $setting->restart2 ?? '17:00:00' }}">
                            </div>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle mr-1"></i>Jadwal ini dikirim ke semua device via MQTT saat tombol "Kirim Setting" ditekan di halaman Device.
                    </small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block mb-4">
                <i class="fas fa-save mr-2"></i>Simpan Setting
            </button>
        </div>

        {{-- ══════════════════════════════════════ --}}
        {{-- TAB 4: SHOLAT --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="tab-pane" id="tab-sholat">
            <div class="card card-outline card-primary mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title"><i class="fas fa-mosque mr-2"></i>Window Waktu Sholat</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Dhuha --}}
                        <div class="col-md-4">
                            <div class="card mb-0">
                                <div class="card-header py-2" style="background:#4caf50; color:#fff;">
                                    <i class="fas fa-sun mr-1"></i>Dhuha
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label style="font-size:12px;">Mulai <small class="text-muted">(dibuka)</small></label>
                                                <input type="time" name="dhuha_start" class="form-control form-control-sm"
                                                    value="{{ $setting->dhuha_start ?? '07:00:00' }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label style="font-size:12px;">Selesai <small class="text-muted">(ditutup)</small></label>
                                                <input type="time" name="dhuha_end" class="form-control form-control-sm"
                                                    value="{{ $setting->dhuha_end ?? '11:00:00' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Dzuhur --}}
                        <div class="col-md-4">
                            <div class="card mb-0">
                                <div class="card-header py-2" style="background:#ff8800; color:#fff;">
                                    <i class="fas fa-mosque mr-1"></i>Dzuhur
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label style="font-size:12px;">Mulai <small class="text-muted">(dibuka)</small></label>
                                                <input type="time" name="dzuhur_start" class="form-control form-control-sm"
                                                    value="{{ $setting->dzuhur_start ?? '11:30:00' }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label style="font-size:12px;">Selesai <small class="text-muted">(ditutup)</small></label>
                                                <input type="time" name="dzuhur_end" class="form-control form-control-sm"
                                                    value="{{ $setting->dzuhur_end ?? '13:30:00' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Ashar --}}
                        <div class="col-md-4">
                            <div class="card mb-0">
                                <div class="card-header py-2" style="background:#9c27b0; color:#fff;">
                                    <i class="fas fa-mosque mr-1"></i>Ashar
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label style="font-size:12px;">Mulai <small class="text-muted">(dibuka)</small></label>
                                                <input type="time" name="ashar_start" class="form-control form-control-sm"
                                                    value="{{ $setting->ashar_start ?? '15:00:00' }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label style="font-size:12px;">Selesai <small class="text-muted">(ditutup)</small></label>
                                                <input type="time" name="ashar_end" class="form-control form-control-sm"
                                                    value="{{ $setting->ashar_end ?? '16:30:00' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle mr-1"></i>Window waktu ini dikirim ke device via MQTT saat setting disimpan atau tombol Set ditekan di halaman Device.</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block mb-4">
                <i class="fas fa-save mr-2"></i>Simpan Setting
            </button>
        </div>

        {{-- ══════════════════════════════════════ --}}
        {{-- TAB 5: INTEGRASI --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="tab-pane" id="tab-integrasi">
            <div class="card card-outline card-info mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Integrasi TIM IT</h3>
                </div>
                <div class="card-body">

                    {{-- Status per URL --}}
                    @php
                        $urlMap = [
                            'presensi_harian' => ['label' => '🔵 Presensi Masuk/Pulang', 'url' => $setting->timid_presensi_url],
                            'presensi_sholat' => ['label' => '🟠 Presensi Sholat',        'url' => $setting->timid_sholat_url],
                            'izin_mens'       => ['label' => '🌸 Izin Menstruasi',         'url' => $setting->timid_izin_mens_url],
                            'izin_keluar'     => ['label' => '🚪 Izin Keluar/Pulang',      'url' => $setting->timid_ijin_url],
                        ];
                    @endphp
                    <div class="row mb-3">
                        @foreach($urlMap as $ep => $info)
                        @php
                            $last = $pushStatus[$ep] ?? null;
                            if (!$info['url']) {
                                $badgeClass = 'badge-secondary';
                                $badgeIcon  = 'fa-minus-circle';
                                $badgeText  = 'Belum diisi';
                            } elseif ($last && $last->status == 1) {
                                $badgeClass = 'badge-success';
                                $badgeIcon  = 'fa-check-circle';
                                $badgeText  = 'OK · ' . \Carbon\Carbon::parse($last->created_at)->format('d/m H:i');
                            } elseif ($last && $last->status == 0) {
                                $badgeClass = 'badge-danger';
                                $badgeIcon  = 'fa-times-circle';
                                $badgeText  = 'Gagal · ' . \Carbon\Carbon::parse($last->created_at)->format('d/m H:i');
                            } else {
                                $badgeClass = 'badge-warning';
                                $badgeIcon  = 'fa-clock';
                                $badgeText  = 'Belum pernah kirim';
                            }
                        @endphp
                        <div class="col-md-3 col-6 mb-2">
                            <div class="p-2 rounded" style="background:rgba(0,0,0,0.05); border:1px solid rgba(0,0,0,0.08);">
                                <div style="font-size:11px; font-weight:600; margin-bottom:4px;">{{ $info['label'] }}</div>
                                <span class="badge {{ $badgeClass }}">
                                    <i class="fas {{ $badgeIcon }} mr-1"></i>{{ $badgeText }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Form URL --}}
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
                                <select name="push_interval" class="form-control form-control-sm">
                                    @foreach([1,2,3,5,10,15,30,60] as $iv)
                                        <option value="{{ $iv }}" {{ (int)($setting->push_interval ?? 5) === $iv ? 'selected' : '' }}>
                                            {{ $iv }} menit
                                        </option>
                                    @endforeach
                                </select>
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
                    <div class="d-flex align-items-center mt-3" style="gap:12px;">
                        <div class="icheck-success">
                            <input type="checkbox" name="push_auto" id="push_auto" value="1"
                                {{ ($setting->push_auto ?? 1) ? 'checked' : '' }}>
                            <label for="push_auto">
                                <i class="fas fa-robot mr-1"></i>Push Otomatis Aktif
                            </label>
                        </div>
                        <small class="text-muted">Jika dicentang, data akan otomatis dikirim ke TIM IT setiap hari jam 22:00 dan re-check mingguan setiap Sabtu jam 23:00</small>
                    </div>
                    <p class="mt-2 mb-0"><small class="text-muted"><i class="fas fa-info-circle mr-1"></i>test push: /opt/lampp/bin/php /opt/lampp/htdocs/siapp-v2/artisan push:presensi --tanggal=2026-06-02 --force</small></p>
                </div>
            </div>

            {{-- Riwayat Push --}}
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title"><i class="fas fa-history mr-2"></i>Riwayat Push ke TIM IT</h3>
                    <div class="card-tools d-flex align-items-center" style="gap:8px;">
                        <small class="text-muted">Tampilkan:</small>
                        @foreach([20, 50, 100, 500] as $limit)
                        <a href="{{ request()->fullUrlWithQuery(['push_log_limit' => $limit, 'tab' => 'integrasi']) }}"
                            class="badge {{ ($pushLogLimit ?? 20) == $limit ? 'badge-primary' : 'badge-secondary' }}">
                            {{ $limit }}
                        </a>
                        @endforeach
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0" style="font-size:12px;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Waktu</th>
                                <th>Endpoint</th>
                                <th>Tanggal Data</th>
                                <th>Total</th>
                                <th>HTTP</th>
                                <th>Status</th>
                                <th>Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pushLog as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m H:i:s') }}</td>
                                <td><span class="badge badge-info">{{ $log->endpoint }}</span></td>
                                <td>{{ $log->tanggal }}</td>
                                <td>{{ $log->total }}</td>
                                <td>{{ $log->http_status ?? '-' }}</td>
                                <td>
                                    @if($log->status)
                                        <span class="badge badge-success">✅ OK</span>
                                    @else
                                        <span class="badge badge-danger">❌ Gagal</span>
                                    @endif
                                </td>
                                <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                    title="{{ $log->pesan }}">
                                    {{ $log->pesan ? substr($log->pesan, 0, 60) : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="fas fa-inbox mr-1"></i>Belum ada riwayat push
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block mb-4">
                <i class="fas fa-save mr-2"></i>Simpan Setting
            </button>
        </div>

        {{-- ══════════════════════════════════════ --}}
        {{-- TAB 6: NOTIFIKASI --}}
        {{-- ══════════════════════════════════════ --}}
        <div class="tab-pane" id="tab-notifikasi">
            <div class="card card-outline card-success mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title"><i class="fab fa-whatsapp mr-2"></i>Notifikasi WhatsApp</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;"><i class="fas fa-phone mr-1"></i>Nomor WA Tujuan</label>
                                <input type="text" name="wa_number" class="form-control form-control-sm"
                                    placeholder="082241863393"
                                    value="{{ $setting->wa_number ?? '082241863393' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;"><i class="fas fa-key mr-1"></i>Device ID Whacenter</label>
                                <input type="text" name="wa_device_id" class="form-control form-control-sm"
                                    style="font-family:monospace;"
                                    placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                    value="{{ $setting->wa_device_id ?? '' }}">
                            </div>
                        </div>
                    </div>
                    {{-- Multi Nomor WA --}}
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label style="font-size:12px;"><i class="fas fa-users mr-1"></i>Nomor WA Tambahan</label>
                            <div id="wa-numbers-container">
                                @php $waNumbers = json_decode($setting->wa_numbers ?? '[]', true) ?? []; @endphp
                                @foreach($waNumbers as $i => $num)
                                <div class="d-flex mb-1" style="gap:6px;">
                                    <input type="text" name="wa_numbers[]"
                                        class="form-control form-control-sm"
                                        placeholder="08xxxxxxxxxx"
                                        value="{{ $num }}">
                                    <button type="button" class="btn btn-sm btn-danger flex-shrink-0"
                                        onclick="this.closest('.d-flex').remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-1" onclick="addWaNumber()">
                                <i class="fas fa-plus mr-1"></i>Tambah Nomor
                            </button>
                            <small class="text-muted d-block mt-1">Jika diisi, notif dikirim ke semua nomor ini. Jika kosong, pakai Nomor WA Tujuan di atas.</small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-clock mr-1"></i>Offline Setelah (detik)</label>
                                <input type="number" name="offline_after" class="form-control form-control-sm"
                                    value="{{ $setting->offline_after ?? 120 }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-exclamation-triangle mr-1"></i>Eskalasi Setelah (detik)</label>
                                <input type="number" name="escalation_after" class="form-control form-control-sm"
                                    value="{{ $setting->escalation_after ?? 300 }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-moon mr-1"></i>Quiet Hours (jam)</label>
                                <div class="d-flex" style="gap:6px;">
                                    <input type="number" name="notif_quiet_start" class="form-control form-control-sm"
                                        placeholder="Mulai" min="0" max="23"
                                        value="{{ $setting->notif_quiet_start ?? 18 }}">
                                    <input type="number" name="notif_quiet_end" class="form-control form-control-sm"
                                        placeholder="Selesai" min="0" max="23"
                                        value="{{ $setting->notif_quiet_end ?? 6 }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;"><i class="fas fa-bell mr-1"></i>Eskalasi Window (jam)</label>
                                <div class="d-flex" style="gap:6px;">
                                    <input type="number" name="notif_escalation_start" class="form-control form-control-sm"
                                        placeholder="Mulai" min="0" max="23"
                                        value="{{ $setting->notif_escalation_start ?? 10 }}">
                                    <input type="number" name="notif_escalation_end" class="form-control form-control-sm"
                                        placeholder="Selesai" min="0" max="23"
                                        value="{{ $setting->notif_escalation_end ?? 16 }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block mb-4">
                <i class="fas fa-save mr-2"></i>Simpan Setting
            </button>
        </div>

    </form>

    {{-- ══════════════════════════════════════ --}}
    {{-- TAB 7: SINKRONISASI --}}
    {{-- ══════════════════════════════════════ --}}
    <div class="tab-pane" id="tab-sinkronisasi">
        <div class="card card-outline card-warning mb-3">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-sync-alt mr-2"></i>Status Sinkronisasi ke TIM IT</h3>
                <div class="card-tools">
                    <small class="text-muted">Tahun Ajaran {{ substr($tglMulai,0,4) }}/{{ substr($tglAkhir,0,4) }}</small>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0" style="font-size:12px;">
                    <thead class="thead-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-center">Presensi</th>
                            <th class="text-center">Dzuhur</th>
                            <th class="text-center">Ashar</th>
                            <th class="text-center">Izin Mens</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapSinkron as $row)
                        @php
                            $adaBelum = false;
                            foreach (['presensi','dzuhur','ashar','izin_mens'] as $k) {
                                if ($row[$k] && $row[$k]->belum > 0) { $adaBelum = true; break; }
                            }
                        @endphp
                        <tr class="{{ $adaBelum ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d M Y') }}</strong>
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('l') }}</small>
                            </td>
                            @foreach(['presensi','dzuhur','ashar','izin_mens'] as $k)
                            <td class="text-center">
                                @if($row[$k])
                                    @if($row[$k]->belum == 0)
                                        <span class="badge badge-success">✅ {{ $row[$k]->total }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $row[$k]->sudah }}/{{ $row[$k]->total }}</span>
                                        <br><small class="text-danger">{{ $row[$k]->belum }} belum</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="text-center">
                                @if($adaBelum)
                                <button class="btn btn-xs btn-warning btn-retry"
                                    data-tanggal="{{ $row['tanggal'] }}"
                                    data-endpoints="all"
                                    title="Push semua yang belum">
                                    <i class="fas fa-sync-alt"></i> Retry
                                </button>
                                @else
                                <span class="text-success" style="font-size:11px;"><i class="fas fa-check"></i> Sinkron</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="fas fa-inbox mr-1"></i>Belum ada data tahun ajaran ini
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
function switchTab(tabId, el) {
    // Sembunyikan semua pane
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    // Nonaktifkan semua tab
    document.querySelectorAll('.setting-tab').forEach(t => t.classList.remove('active'));
    // Aktifkan tab & pane yang dipilih
    document.getElementById('tab-' + tabId).classList.add('active');
    el.classList.add('active');
    // Simpan state ke localStorage
    localStorage.setItem('setting_tab', tabId);
}

// Restore tab terakhir
document.addEventListener('DOMContentLoaded', function() {
    const last = localStorage.getItem('setting_tab');
    if (last) {
        const tab = document.querySelector(`.setting-tab[onclick*="${last}"]`);
        if (tab) switchTab(last, tab);
    }
});

// ── Retry Push ──
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-retry').forEach(btn => {
        btn.addEventListener('click', function() {
            const tanggal   = this.dataset.tanggal;
            const endpoints = this.dataset.endpoints;
            const btnEl     = this;

            btnEl.disabled = true;
            btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Proses...';

            fetch('{{ route("setting.retry-push") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tanggal, endpoints: [endpoints] })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'ok') {
                    btnEl.innerHTML = '<i class="fas fa-check"></i> Selesai';
                    btnEl.classList.replace('btn-warning', 'btn-success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="fas fa-sync-alt"></i> Retry';
                    alert('Error: ' + data.message);
                }
            })
            .catch(() => {
                btnEl.disabled = false;
                btnEl.innerHTML = '<i class="fas fa-sync-alt"></i> Retry';
                alert('Gagal menghubungi server');
            });
        });
    });
});

function addWaNumber() {
    const container = document.getElementById('wa-numbers-container');
    const div = document.createElement('div');
    div.className = 'd-flex mb-1';
    div.style.gap = '6px';
    div.innerHTML = `
        <input type="text" name="wa_numbers[]"
            class="form-control form-control-sm"
            placeholder="08xxxxxxxxxx">
        <button type="button" class="btn btn-sm btn-danger flex-shrink-0"
            onclick="this.closest('.d-flex').remove()">
            <i class="fas fa-times"></i>
        </button>`;
    container.appendChild(div);
}
</script>
@endpush