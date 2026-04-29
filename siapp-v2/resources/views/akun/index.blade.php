@extends('layouts.app')
@section('title', 'Manajemen Akun')
@section('page_title', 'Manajemen Akun')

@push('styles')
<style>
.avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 14px;
    flex-shrink: 0;
}
.akun-table th { font-size: 11px; }
.akun-table td { font-size: 12px; vertical-align: middle; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('error') }}
</div>
@endif

<div class="d-flex mb-3">
    <button class="btn btn-success btn-sm ml-auto" data-toggle="modal" data-target="#modalTambah">
        <i class="fas fa-user-plus mr-1"></i>Tambah Akun
    </button>
</div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>Daftar Akun Admin
            <span class="badge badge-primary ml-2">{{ $akuns->count() }}</span>
        </h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover akun-table mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Akun</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Terakhir Login</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($akuns as $i => $a)
                @php $isSelf = session('admin_id') == $a->id; @endphp
                <tr class="{{ $isSelf ? 'table-info' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center" style="gap:10px;">
                            <div class="avatar">{{ strtoupper(substr($a->username, 0, 1)) }}</div>
                            <div>
                                <strong>{{ $a->username }}</strong>
                                @if($isSelf)
                                    <span class="badge badge-info ml-1" style="font-size:9px;">Anda</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $a->email }}</td>
                    <td>
                        @if($a->wa)
                            <a href="https://wa.me/{{ $a->wa }}" target="_blank" class="text-success">
                                <i class="fab fa-whatsapp mr-1"></i>{{ $a->wa }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $a->timestamp }}</small></td>
                    <td>
                        <div class="d-flex" style="gap:4px;">
                            <button class="btn btn-xs btn-warning"
                                onclick="editAkun({{ $a->id }}, '{{ $a->username }}', '{{ $a->email }}', '{{ $a->wa }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-xs btn-info"
                                onclick="resetPass({{ $a->id }}, '{{ $a->username }}')">
                                <i class="fas fa-key"></i>
                            </button>
                            @if(!$isSelf)
                            <form action="{{ route('akun.destroy', $a->id) }}" method="POST"
                                onsubmit="return confirm('Hapus akun {{ $a->username }}?')">
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

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Tambah Akun</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('akun.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control"
                            minlength="6" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                            </div>
                            <input type="text" name="wa" class="form-control"
                                placeholder="628xxxxxxxxxx">
                        </div>
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
                <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Edit Akun</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                            </div>
                            <input type="text" name="wa" id="edit-wa" class="form-control">
                        </div>
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

{{-- Modal Reset Password --}}
<div class="modal fade" id="modalPassword" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-key mr-2"></i>Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formPassword" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <p class="text-muted" style="font-size:12px;">
                        Reset password untuk: <strong id="reset-nama"></strong>
                    </p>
                    <div class="form-group">
                        <label>Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control"
                            minlength="6" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-key mr-1"></i>Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editAkun(id, username, email, wa) {
    document.getElementById('edit-username').value = username;
    document.getElementById('edit-email').value    = email;
    document.getElementById('edit-wa').value       = wa || '';
    document.getElementById('formEdit').action     = '/akun/' + id;
    $('#modalEdit').modal('show');
}

function resetPass(id, nama) {
    document.getElementById('reset-nama').textContent   = nama;
    document.getElementById('formPassword').action      = '/akun/' + id + '/password';
    $('#modalPassword').modal('show');
}
</script>
@endpush