@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('page_title', 'Edit Siswa')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Edit Siswa</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NIS</label>
                        <input type="text" class="form-control" value="{{ $siswa->nis }}" disabled>
                        <small class="text-muted">NIS tidak bisa diubah</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No Kartu RFID</label>
                        <input type="text" name="nokartu" class="form-control"
                            maxlength="8" style="font-family:monospace;font-weight:700;"
                            value="{{ $siswa->nokartu }}"
                            oninput="this.value=this.value.toUpperCase()">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control"
                    value="{{ $siswa->nama }}" required
                    oninput="this.value=this.value.toUpperCase()">
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tingkat</label>
                        <select name="tingkat" class="form-control">
                            <option value="X"   {{ $siswa->tingkat==='X'   ? 'selected':'' }}>X</option>
                            <option value="XI"  {{ $siswa->tingkat==='XI'  ? 'selected':'' }}>XI</option>
                            <option value="XII" {{ $siswa->tingkat==='XII' ? 'selected':'' }}>XII</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jurusan</label>
                        <input type="text" name="jur" class="form-control" value="{{ $siswa->jur }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kode Kelas</label>
                        <input type="text" name="kode" class="form-control" value="{{ $siswa->kode }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Kelas <span class="text-danger">*</span></label>
                <input type="text" name="kelas" class="form-control"
                    value="{{ $siswa->kelas }}" required>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control"
                    value="{{ $siswa->keterangan }}">
            </div>
            <div class="d-flex" style="gap:8px;">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i>Update
                </button>
                <a href="{{ route('siswa') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection