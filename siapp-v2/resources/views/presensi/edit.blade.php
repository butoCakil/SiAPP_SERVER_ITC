@extends('layouts.app')
@section('title', 'Edit Presensi')
@section('page_title', 'Edit Presensi')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Presensi</h3>
    </div>
    <div class="card-body">

        <div class="alert alert-light border mb-4">
            <strong>{{ $presensi->nama }}</strong>
            <span class="text-muted ml-2">{{ $presensi->nomorinduk }}</span>
            <span class="badge badge-info ml-2">{{ $presensi->info }}</span>
            <span class="badge badge-secondary ml-1">{{ $presensi->tanggal }}</span>
        </div>

        <form action="{{ route('presensi.update', $presensi->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="tanggal" value="{{ $presensi->tanggal }}">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Waktu Masuk</label>
                        <input type="time" name="waktumasuk" class="form-control"
                            step="1" value="{{ $presensi->waktumasuk }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ket Masuk</label>
                        <select name="ketmasuk" class="form-control">
                            <option value="TW"  {{ $presensi->ketmasuk==='TW'  ? 'selected':'' }}>Tepat Waktu</option>
                            <option value="TL"  {{ $presensi->ketmasuk==='TL'  ? 'selected':'' }}>Toleransi</option>
                            <option value="TLT" {{ $presensi->ketmasuk==='TLT' ? 'selected':'' }}>Terlambat</option>
                            <option value="T"   {{ $presensi->ketmasuk==='T'   ? 'selected':'' }}>Telat</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Waktu Pulang</label>
                        <input type="time" name="waktupulang" class="form-control"
                            step="1" value="{{ ($presensi->waktupulang && $presensi->waktupulang !== '00:00:00') ? $presensi->waktupulang : '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ket Pulang</label>
                        <select name="ketpulang" class="form-control">
                            <option value="">— Belum Pulang —</option>
                            <option value="PLG" {{ $presensi->ketpulang==='PLG' ? 'selected':'' }}>Normal</option>
                            <option value="PA"  {{ $presensi->ketpulang==='PA'  ? 'selected':'' }}>Pulang Awal</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control"
                    value="{{ $presensi->keterangan }}" placeholder="Opsional...">
            </div>

            <div class="d-flex" style="gap:8px;">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i>Update
                </button>
                <a href="{{ route('presensi', ['tanggal'=>$presensi->tanggal]) }}"
                    class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection