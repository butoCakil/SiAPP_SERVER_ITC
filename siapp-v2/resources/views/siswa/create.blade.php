@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('page_title', 'Tambah Siswa')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Tambah Siswa</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('siswa.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control"
                            value="{{ old('nis') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No Kartu RFID</label>
                        <input type="text" name="nokartu" class="form-control"
                            maxlength="8" style="font-family:monospace;font-weight:700;"
                            placeholder="8 karakter hex..."
                            value="{{ old('nokartu') }}"
                            oninput="this.value=this.value.toUpperCase()">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control"
                    value="{{ old('nama') }}" required
                    oninput="this.value=this.value.toUpperCase()">
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tingkat <span class="text-danger">*</span></label>
                        <select name="tingkat" class="form-control" required>
                            <option value="">--</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jurusan</label>
                        <input type="text" name="jur" class="form-control"
                            value="{{ old('jur') }}" placeholder="TE, AT, dll">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kode Kelas</label>
                        <input type="text" name="kode" class="form-control"
                            value="{{ old('kode') }}" placeholder="XTE1, XIAT2...">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Kelas <span class="text-danger">*</span></label>
                <input type="text" name="kelas" class="form-control"
                    value="{{ old('kelas') }}" required placeholder="X TE 1, XI AT 2...">
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control"
                    value="{{ old('keterangan') }}">
            </div>
            <div class="d-flex" style="gap:8px;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-1"></i>Simpan
                </button>
                <a href="{{ route('siswa') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection