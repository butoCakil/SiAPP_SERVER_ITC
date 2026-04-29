@extends('layouts.app')
@section('title', 'API Key Management')
@section('page_title', 'API Key Management')

@push('styles')
<style>
.key-code {
    font-family: monospace;
    font-size: 12px;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 4px 10px;
    word-break: break-all;
    position: relative;
}
.key-code.blur-text { filter: blur(4px); transition: filter 0.2s; cursor: pointer; }
.key-code.blur-text:hover { filter: blur(0); }
.badge-device  { background: #1565c0; color:#fff; }
.badge-sim     { background: #6a1b9a; color:#fff; }
.badge-lama    { background: #757575; color:#fff; }
.badge-aktif   { background: #00c853; color:#fff; }
.badge-nonaktif{ background: #f44336; color:#fff; }
.jenis-card {
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
}
.jenis-card.device { border-left: 4px solid #1565c0; }
.jenis-card.sim    { border-left: 4px solid #6a1b9a; }
.jenis-card.lama   { border-left: 4px solid #9e9e9e; opacity: 0.7; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

<div class="d-flex mb-3">
    <button class="btn btn-success btn-sm ml-auto" data-toggle="modal" data-target="#modalTambah">
        <i class="fas fa-plus mr-1"></i>Tambah API Key
    </button>
</div>

{{-- Info --}}
<div class="alert alert-info">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Device Token</strong> — digunakan ESP32 untuk autentikasi ke server.
    <strong class="ml-3">SIM Token</strong> — digunakan TIM IT untuk akses REST API data presensi.
    <br>
    <small class="text-muted">Hover pada token untuk melihat nilai lengkap. Klik ikon copy untuk menyalin.</small>
</div>

{{-- List Keys --}}
@foreach(['device_token' => ['label'=>'Device Token (ESP32)', 'class'=>'device', 'badge'=>'badge-device'],
          'sim_token'    => ['label'=>'SIM Token (TIM IT)',    'class'=>'sim',    'badge'=>'badge-sim'],
          'lama'         => ['label'=>'Token Lama',            'class'=>'lama',   'badge'=>'badge-lama']]
    as $jenis => $conf)

@php $filtered = $keys->where('jenis', $jenis); @endphp
@if($filtered->count() > 0)
<div class="jenis-card {{ $conf['class'] }}">
    <div class="d-flex align-items-center mb-3">
        <h5 class="mb-0 mr-2">{{ $conf['label'] }}</h5>
        <span class="badge {{ $conf['badge'] }}">{{ $filtered->count() }} key</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Info</th>
                    <th>Token</th>
                    <th>Masa Berlaku</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($filtered as $i => $key)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $key->info_api ?? '-' }}</strong></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span class="key-code blur-text" id="key-{{ $key->id }}"
                                title="Hover untuk lihat">
                                {{ $key->kode_api }}
                            </span>
                            <button class="btn btn-xs btn-outline-secondary"
                                onclick="copyKey('{{ $key->kode_api }}')"
                                title="Copy token">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        @php $expired = $key->masaberlaku < date('Y-m-d'); @endphp
                        <span class="{{ $expired ? 'text-danger' : 'text-success' }}">
                            {{ $key->masaberlaku }}
                            @if($expired) <small>(Expired)</small> @endif
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $key->status === 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                            {{ $key->status }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex" style="gap:4px;">
                            <button class="btn btn-xs btn-warning"
                                onclick="editKey({{ $key->id }}, '{{ $key->kode_api }}', '{{ $key->info_api }}', '{{ $key->jenis }}', '{{ $key->masaberlaku }}', '{{ $key->status }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($key->jenis === 'lama')
                            <form action="{{ route('apikey.destroy', $key->id) }}" method="POST"
                                onsubmit="return confirm('Hapus API Key ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah API Key</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('apikey.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Info / Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="info_api" class="form-control"
                            placeholder="Contoh: Token Device Masjid, Token TIM IT..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis <span class="text-danger">*</span></label>
                                <select name="jenis" class="form-control" required>
                                    <option value="device_token">Device Token</option>
                                    <option value="sim_token">SIM Token</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="aktif">aktif</option>
                                    <option value="nonaktif">nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Masa Berlaku <span class="text-danger">*</span></label>
                        <input type="date" name="masaberlaku" class="form-control"
                            value="{{ date('Y', strtotime('+1 year')) }}-12-31" required>
                    </div>
                    <div class="form-group">
                        <label>Token (opsional — kosongkan untuk generate otomatis)</label>
                        <input type="text" name="kode_api" class="form-control"
                            style="font-family:monospace;"
                            placeholder="Kosongkan untuk generate otomatis...">
                        <small class="text-muted">Jika diisi, token ini yang akan digunakan</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i>Simpan
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
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit API Key</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Token Saat Ini</label>
                        <div class="key-code blur-text" id="edit-token-display">-</div>
                    </div>
                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" name="regenerate" id="cb-regenerate" value="1">
                            <label for="cb-regenerate">
                                <i class="fas fa-sync mr-1"></i>Generate token baru (otomatis)
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Info / Keterangan</label>
                        <input type="text" name="info_api" id="edit-info" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis</label>
                                <select name="jenis" id="edit-jenis" class="form-control">
                                    <option value="device_token">Device Token</option>
                                    <option value="sim_token">SIM Token</option>
                                    <option value="lama">Lama</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="edit-status" class="form-control">
                                    <option value="aktif">aktif</option>
                                    <option value="nonaktif">nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Masa Berlaku</label>
                        <input type="date" name="masaberlaku" id="edit-masa" class="form-control">
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
function editKey(id, kode, info, jenis, masa, status) {
    document.getElementById('edit-token-display').textContent = kode;
    document.getElementById('edit-info').value   = info;
    document.getElementById('edit-jenis').value  = jenis;
    document.getElementById('edit-masa').value   = masa;
    document.getElementById('edit-status').value = status;
    document.getElementById('formEdit').action   = '/apikey/' + id;
    document.getElementById('cb-regenerate').checked = false;
    $('#modalEdit').modal('show');
}

function copyKey(token) {
    navigator.clipboard.writeText(token).then(() => {
        toastr.success('Token berhasil disalin!');
    }).catch(() => {
        alert('Token: ' + token);
    });
}
</script>
@endpush