@extends('layouts.app')

@section('title', 'Setting')
@section('page_title', 'Setting Presensi')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">

            {{-- Auto/Manual Toggle --}}
            <div class="card card-outline {{ $setting->auto_mode ? 'card-success' : 'card-warning' }} mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-robot mr-2"></i>Mode Operasi
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5>
                                @if ($setting->auto_mode)
                                    <span class="badge badge-success p-2">
                                        <i class="fas fa-robot mr-1"></i>OTOMATIS
                                    </span>
                                    <small class="text-muted ml-2">Presensi dibuka/tutup otomatis sesuai jadwal</small>
                                @else
                                    <span class="badge badge-warning p-2">
                                        <i class="fas fa-hand-paper mr-1"></i>MANUAL
                                    </span>
                                    <small class="text-muted ml-2">Admin kontrol buka/tutup presensi</small>
                                @endif
                            </h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <form action="{{ route('setting.update') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="mode" value="{{ $setting->mode }}">
                                <input type="hidden" name="wa" value="{{ $setting->wa }}">
                                <input type="hidden" name="wta" value="{{ $setting->wta }}">
                                <input type="hidden" name="wtp" value="{{ $setting->wtp }}">
                                <input type="hidden" name="wtp_jumat" value="{{ $setting->wtp_jumat }}">
                                <input type="hidden" name="wp" value="{{ $setting->wp }}">
                                <input type="hidden" name="wp_jumat" value="{{ $setting->wp_jumat }}">
                                <input type="hidden" name="hari_kerja" value="{{ $setting->hari_kerja }}">
                                <input type="hidden" name="waktumasuk" value="{{ $setting->waktumasuk }}">
                                <input type="hidden" name="waktupulang" value="{{ $setting->waktupulang }}">
                                <input type="hidden" name="info" value="{{ $setting->info }}">
                                <input type="hidden" name="auto_mode" value="{{ $setting->auto_mode ? 0 : 1 }}">
                                <button type="submit"
                                    class="btn {{ $setting->auto_mode ? 'btn-warning' : 'btn-success' }}">
                                    <i class="fas {{ $setting->auto_mode ? 'fa-hand-paper' : 'fa-robot' }} mr-1"></i>
                                    Ganti ke {{ $setting->auto_mode ? 'MANUAL' : 'OTOMATIS' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Manual Controls --}}
                    @if (!$setting->auto_mode)
                        <hr>
                        <div class="row text-center mt-2">
                            <div class="col-md-4 mb-2">
                                <form action="{{ route('setting.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="auto_mode" value="0">
                                    <input type="hidden" name="wa" value="{{ $setting->wa }}">
                                    <input type="hidden" name="wta" value="{{ $setting->wta }}">
                                    <input type="hidden" name="wtp" value="{{ $setting->wtp }}">
                                    <input type="hidden" name="wtp_jumat" value="{{ $setting->wtp_jumat }}">
                                    <input type="hidden" name="wp" value="{{ $setting->wp }}">
                                    <input type="hidden" name="wp_jumat" value="{{ $setting->wp_jumat }}">
                                    <input type="hidden" name="hari_kerja" value="{{ $setting->hari_kerja }}">
                                    <input type="hidden" name="waktumasuk" value="{{ $setting->waktumasuk }}">
                                    <input type="hidden" name="waktupulang" value="{{ $setting->waktupulang }}">
                                    <input type="hidden" name="info" value="{{ $setting->info }}">
                                    <button type="submit" name="mode" value="0"
                                        class="btn btn-block p-3 {{ $setting->mode == 0 ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                        <i class="fas fa-ban fa-2x mb-1 d-block"></i>
                                        <strong>TUTUP</strong>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4 mb-2">
                                <form action="{{ route('setting.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="auto_mode" value="0">
                                    <input type="hidden" name="wa" value="{{ $setting->wa }}">
                                    <input type="hidden" name="wta" value="{{ $setting->wta }}">
                                    <input type="hidden" name="wtp" value="{{ $setting->wtp }}">
                                    <input type="hidden" name="wtp_jumat" value="{{ $setting->wtp_jumat }}">
                                    <input type="hidden" name="wp" value="{{ $setting->wp }}">
                                    <input type="hidden" name="wp_jumat" value="{{ $setting->wp_jumat }}">
                                    <input type="hidden" name="hari_kerja" value="{{ $setting->hari_kerja }}">
                                    <input type="hidden" name="waktumasuk" value="{{ $setting->waktumasuk }}">
                                    <input type="hidden" name="waktupulang" value="{{ $setting->waktupulang }}">
                                    <input type="hidden" name="info" value="{{ $setting->info }}">
                                    <button type="submit" name="mode" value="1"
                                        class="btn btn-block p-3 {{ $setting->mode == 1 ? 'btn-success' : 'btn-outline-success' }}">
                                        <i class="fas fa-door-open fa-2x mb-1 d-block"></i>
                                        <strong>MASUK</strong>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4 mb-2">
                                <form action="{{ route('setting.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="auto_mode" value="0">
                                    <input type="hidden" name="wa" value="{{ $setting->wa }}">
                                    <input type="hidden" name="wta" value="{{ $setting->wta }}">
                                    <input type="hidden" name="wtp" value="{{ $setting->wtp }}">
                                    <input type="hidden" name="wtp_jumat" value="{{ $setting->wtp_jumat }}">
                                    <input type="hidden" name="wp" value="{{ $setting->wp }}">
                                    <input type="hidden" name="wp_jumat" value="{{ $setting->wp_jumat }}">
                                    <input type="hidden" name="hari_kerja" value="{{ $setting->hari_kerja }}">
                                    <input type="hidden" name="waktumasuk" value="{{ $setting->waktumasuk }}">
                                    <input type="hidden" name="waktupulang" value="{{ $setting->waktupulang }}">
                                    <input type="hidden" name="info" value="{{ $setting->info }}">
                                    <button type="submit" name="mode" value="2"
                                        class="btn btn-block p-3 {{ $setting->mode == 2 ? 'btn-warning' : 'btn-outline-warning' }}">
                                        <i class="fas fa-door-closed fa-2x mb-1 d-block"></i>
                                        <strong>PULANG</strong>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Jadwal Waktu --}}
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Jadwal & Waktu</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('setting.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mode" value="{{ $setting->mode }}">
                        <input type="hidden" name="auto_mode" value="{{ $setting->auto_mode }}">

                        {{-- Hari Kerja --}}
                        <div class="form-group">
                            <label><i class="fas fa-calendar-week mr-1"></i>Hari Kerja</label>
                            <div class="d-flex">
                                <div class="mr-4">
                                    <div class="icheck-success">
                                        <input type="radio" name="hari_kerja" id="hk5" value="5"
                                            {{ $setting->hari_kerja == 5 ? 'checked' : '' }}>
                                        <label for="hk5">5 Hari (Senin - Jumat)</label>
                                    </div>
                                </div>
                                <div>
                                    <div class="icheck-primary">
                                        <input type="radio" name="hari_kerja" id="hk6" value="6"
                                            {{ $setting->hari_kerja == 6 ? 'checked' : '' }}>
                                        <label for="hk6">6 Hari (Senin - Sabtu)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-school mr-1"></i>Tingkat Aktif</label>
                                <div class="d-flex gap-3" style="gap:16px;">
                                    @foreach(['X','XI','XII'] as $t)
                                    <div class="icheck-success">
                                        <input type="checkbox" name="tingkat_aktif[]"
                                            id="tingkat-{{ $t }}" value="{{ $t }}"
                                            {{ in_array($t, json_decode($setting->tingkat_aktif ?? '["X","XI","XII"]', true)) ? 'checked' : '' }}>
                                        <label for="tingkat-{{ $t }}">Tingkat {{ $t }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Tingkat yang tidak dicentang akan disembunyikan dari rekap</small>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Hari Biasa --}}
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-sun mr-1"></i>Hari Biasa (Senin -
                                        Kamis{{ $setting->hari_kerja == 6 ? ', Sabtu' : '' }})
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Jam Masuk Resmi</label>
                                            <input type="time" name="waktumasuk" class="form-control"
                                                value="{{ $setting->waktumasuk }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Batas Tepat Waktu <small class="text-muted">(presensi
                                                    dibuka)</small></label>
                                            <input type="time" name="wa" class="form-control"
                                                value="{{ $setting->wa }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Batas Toleransi <small class="text-muted">(presensi
                                                    ditutup)</small></label>
                                            <input type="time" name="wta" class="form-control"
                                                value="{{ $setting->wta }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Jam Pulang Resmi</label>
                                            <input type="time" name="waktupulang" class="form-control"
                                                value="{{ $setting->waktupulang }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Batas Pulang Awal <small class="text-muted">(presensi pulang
                                                    dibuka)</small></label>
                                            <input type="time" name="wtp" class="form-control"
                                                value="{{ $setting->wtp }}">
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Batas Akhir Pulang <small class="text-muted">(presensi
                                                    ditutup)</small></label>
                                            <input type="time" name="wp" class="form-control"
                                                value="{{ $setting->wp }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hari Jumat --}}
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <i class="fas fa-mosque mr-1"></i>Hari Jumat
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info p-2 mb-3">
                                            <small><i class="fas fa-info-circle mr-1"></i>
                                                Jam masuk Jumat sama dengan hari biasa</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Batas Pulang Awal Jumat <small class="text-muted">(presensi pulang
                                                    dibuka)</small></label>
                                            <input type="time" name="wtp_jumat" class="form-control"
                                                value="{{ $setting->wtp_jumat }}">
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Batas Akhir Pulang Jumat <small class="text-muted">(presensi
                                                    ditutup)</small></label>
                                            <input type="time" name="wp_jumat" class="form-control"
                                                value="{{ $setting->wp_jumat }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Integrasi TIM IT --}}
                        <div class="card card-outline card-info mb-3">
                            <div class="card-header py-2">
                                <h3 class="card-title" style="font-size:13px;">
                                    <i class="fas fa-exchange-alt mr-1"></i>Integrasi TIM IT
                                </h3>
                            </div>
                            <div class="card-body">
                                @php
                                    $urlPresensi = env('TIMID_PRESENSI_URL', '');
                                    $urlSholat   = env('TIMID_SHOLAT_URL', '');
                                    $sudahDiatur = $urlPresensi || $urlSholat;
                                @endphp
                                <div class="mb-3">
                                    @if($sudahDiatur)
                                        <span class="badge badge-success p-2">
                                            <i class="fas fa-check-circle mr-1"></i>Terhubung ke TIM IT
                                        </span>
                                    @else
                                        <span class="badge badge-warning p-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Belum dikonfigurasi
                                        </span>
                                        <small class="text-muted ml-2">
                                            Isi <code>TIMID_PRESENSI_URL</code> dan <code>TIMID_SHOLAT_URL</code> di file <code>.env</code>
                                        </small>
                                    @endif
                                </div>
                                <div class="form-group mb-0">
                                    <label><i class="fas fa-sync mr-1"></i>Interval Push</label>
                                    <div class="d-flex align-items-center" style="gap:8px;">
                                        <select name="push_interval" class="form-control" style="width:120px;">
                                            @foreach([5,10,15,30,60] as $interval)
                                                <option value="{{ $interval }}"
                                                    {{ (int)env('PUSH_INTERVAL', 5) === $interval ? 'selected' : '' }}>
                                                    {{ $interval }} menit
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Data presensi dikirim ke SIM setiap interval ini</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>

                        <input type="hidden" name="info" value="{{ $setting->info ?? '' }}">

                        <button type="submit" class="btn btn-primary btn-block mt-2">
                            <i class="fas fa-save mr-2"></i>Simpan Jadwal
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
