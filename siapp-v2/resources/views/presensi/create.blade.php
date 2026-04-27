@extends('layouts.app')
@section('title', 'Tambah Presensi')
@section('page_title', 'Tambah Presensi Manual')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus mr-2"></i>Tambah Presensi Manual</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('presensi.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Siswa <span class="text-danger">*</span></label>
                <select name="nokartu" class="form-control" id="siswa-select" required
                    onchange="fillSiswa(this)">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswaList as $s)
                        <option value="{{ $s->nokartu }}"
                            data-nis="{{ $s->nis }}"
                            data-nama="{{ $s->nama }}"
                            data-kelas="{{ $s->kelas }}">
                            {{ $s->nis }} — {{ $s->nama }} ({{ $s->kelas }})
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="nomorinduk" id="inp-nis">
            <input type="hidden" name="nama"       id="inp-nama">
            <input type="hidden" name="info"       id="inp-kelas">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Waktu Masuk <span class="text-danger">*</span></label>
                        <input type="time" name="waktumasuk" class="form-control"
                            step="1" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ket Masuk</label>
                        <select name="ketmasuk" class="form-control">
                            <option value="TW">Tepat Waktu</option>
                            <option value="TL">Toleransi</option>
                            <option value="TLT">Terlambat</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Waktu Pulang</label>
                        <input type="time" name="waktupulang" class="form-control" step="1">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ket Pulang</label>
                        <select name="ketpulang" class="form-control">
                            <option value="">— Belum Pulang —</option>
                            <option value="PLG">Normal</option>
                            <option value="PA">Pulang Awal</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                            placeholder="Opsional...">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2" style="gap:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Simpan
                </button>
                <a href="{{ route('presensi') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
function fillSiswa(sel) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('inp-nis').value   = opt.dataset.nis   || '';
    document.getElementById('inp-nama').value  = opt.dataset.nama  || '';
    document.getElementById('inp-kelas').value = opt.dataset.kelas || '';
}
</script>
@endpush