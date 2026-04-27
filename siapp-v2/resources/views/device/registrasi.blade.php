@extends('layouts.app')
@section('title', 'Registrasi Device')
@section('page_title', 'Registrasi Device')

@push('styles')
<style>
.reg-table th { font-size: 11px; white-space: nowrap; }
.reg-table td { font-size: 12px; vertical-align: middle; }
.badge-terdaftar { background:#00c853; color:#fff; }
.badge-nonaktif  { background:#f44336; color:#fff; }
.badge-aktif     { background:#2196f3; color:#fff; }
</style>
@endpush

@section('content')

<div class="d-flex mb-3" style="gap:8px;">
    <a href="{{ route('device') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i>Monitor Device
    </a>
    <button class="btn btn-success btn-sm ml-auto" data-toggle="modal" data-target="#modalTambah">
        <i class="fas fa-plus mr-1"></i>Daftarkan Device Baru
    </button>
</div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>Daftar Device Terdaftar
            <span class="badge badge-primary ml-2">{{ $regDevices->count() }} device</span>
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover reg-table mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Chip ID</th>
                        <th>No Device</th>
                        <th>Kode</th>
                        <th>Info Device</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regDevices as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><code>{{ $d->chip_id }}</code></td>
                        <td><strong>{{ $d->no_device }}</strong></td>
                        <td>
                            <span class="badge badge-secondary">{{ $d->kode }}</span>
                        </td>
                        <td>{{ $d->info_device }}</td>
                        <td>
                            <span class="badge {{ $d->status === 'terdaftar' ? 'badge-terdaftar' : ($d->status === 'aktif' ? 'badge-aktif' : 'badge-nonaktif') }}">
                                {{ $d->status }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex" style="gap:4px;">
                                <button class="btn btn-xs btn-warning"
                                    onclick="editDevice({{ $d->id }}, '{{ $d->chip_id }}', '{{ $d->no_device }}', '{{ $d->kode }}', '{{ $d->info_device }}', '{{ $d->status }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('device.registrasi.destroy', $d->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus device {{ $d->no_device }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-mobile-alt fa-2x mb-2 d-block"></i>
                            Belum ada device terdaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Daftarkan Device Baru</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('device.registrasi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Chip ID <span class="text-danger">*</span></label>
                        <input type="text" name="chip_id" class="form-control"
                            placeholder="Contoh: 16581755" required
                            value="{{ old('chip_id') }}">
                        <small class="text-muted">ID chip ESP32 (desimal)</small>
                    </div>
                    <div class="form-group">
                        <label>No Device <span class="text-danger">*</span></label>
                        <input type="text" name="no_device" class="form-control"
                            placeholder="Contoh: 2309MAS001" required
                            oninput="this.value=this.value.toUpperCase()"
                            value="{{ old('no_device') }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode <span class="text-danger">*</span></label>
                                <select name="kode" class="form-control" required>
                                    @foreach($kodeList as $k)
                                        <option value="{{ $k }}" {{ old('kode')===$k ? 'selected':'' }}>{{ $k }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="terdaftar">terdaftar</option>
                                    <option value="aktif">aktif</option>
                                    <option value="nonaktif">nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Info Device <span class="text-danger">*</span></label>
                        <input type="text" name="info_device" class="form-control"
                            placeholder="Contoh: Masjid 1, Gerbang Utama..." required
                            value="{{ old('info_device') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i>Daftarkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Device</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>No Device</label>
                        <input type="text" id="edit-nodevice" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Chip ID</label>
                        <input type="text" name="chip_id" id="edit-chipid" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode</label>
                                <select name="kode" id="edit-kode" class="form-control">
                                    @foreach($kodeList as $k)
                                        <option value="{{ $k }}">{{ $k }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="edit-status" class="form-control">
                                    <option value="terdaftar">terdaftar</option>
                                    <option value="aktif">aktif</option>
                                    <option value="nonaktif">nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Info Device</label>
                        <input type="text" name="info_device" id="edit-info" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editDevice(id, chipId, noDevice, kode, infoDevice, status) {
    document.getElementById('edit-nodevice').value = noDevice;
    document.getElementById('edit-chipid').value   = chipId;
    document.getElementById('edit-info').value     = infoDevice;
    document.getElementById('edit-kode').value     = kode;
    document.getElementById('edit-status').value   = status;
    document.getElementById('formEdit').action     = '/device/registrasi/' + id;
    $('#modalEdit').modal('show');
}
</script>
@endpush