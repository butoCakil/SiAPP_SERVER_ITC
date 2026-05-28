@extends('layouts.app')
@section('title', 'Izin Keluar')
@section('page_title', 'Izin Keluar / Pulang')

@push('styles')
<style>
.stat-card-sm {
    border-radius: 10px;
    padding: 14px 18px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.stat-card-sm .val { font-size: 1.6em; font-weight: 700; line-height: 1; }
.stat-card-sm .lbl { font-size: 11px; opacity: 0.85; }
.sc-belum { background: linear-gradient(135deg,#f44336,#c62828); }
.sc-sudah { background: linear-gradient(135deg,#00c853,#00701a); }

.badge-belum { background:#f44336; color:#fff; }
.badge-sudah { background:#00c853; color:#fff; }

.ijin-table th { font-size: 11px; white-space: nowrap; }
.ijin-table td { font-size: 12px; vertical-align: middle; }

@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
    @page { size: landscape; margin: 10mm; }
    .print-header { display: block !important; }
}
.print-header { display: none; margin-bottom: 10px; }
</style>
@endpush

@section('content')

{{-- Stat Cards --}}
<div class="row no-print">
    <div class="col-md-3 col-6">
        <div class="stat-card-sm sc-belum">
            <div class="s-icon" style="font-size:1.8em;">🚪</div>
            <div>
                <div class="val">{{ $totalBelumKembali }}</div>
                <div class="lbl">Belum Kembali</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card-sm sc-sudah">
            <div class="s-icon" style="font-size:1.8em;">✅</div>
            <div>
                <div class="val">{{ $totalSudahKembali }}</div>
                <div class="lbl">Sudah Kembali</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="d-flex flex-wrap mb-3 no-print" style="gap:8px;">
    <form action="{{ route('presensi.ijin') }}" method="GET"
        class="d-flex flex-wrap align-items-center" style="gap:8px;">
        <div class="input-group" style="width:auto;">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" name="tanggal" class="form-control"
                value="{{ $tanggal }}" onchange="this.form.submit()">
        </div>
        <select name="kelas" class="form-control" style="width:160px;" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k }}" {{ $filterKelas==$k ? 'selected':'' }}>{{ $k }}</option>
            @endforeach
        </select>
        <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="belum" {{ $filterStatus==='belum' ? 'selected':'' }}>Belum Kembali</option>
            <option value="sudah" {{ $filterStatus==='sudah' ? 'selected':'' }}>Sudah Kembali</option>
        </select>
        <a href="{{ route('presensi.ijin') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        <input type="text" id="searchInput" class="form-control" style="width:180px;"
            placeholder="🔍 Cari nama...">
    </form>
    <div class="ml-auto d-flex" style="gap:8px;">
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
            <i class="fas fa-print mr-1"></i>Print PDF
        </button>
        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus mr-1"></i>Tambah Manual
        </button>
    </div>
</div>

{{-- Print Area --}}
<div id="print-area">
    <div class="print-header">
        <strong>Rekap Izin Keluar/Pulang</strong> —
        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
        @if($filterKelas) | Kelas: {{ $filterKelas }} @endif
        <br>
        <small>Belum Kembali: {{ $totalBelumKembali }} | Sudah Kembali: {{ $totalSudahKembali }}</small>
    </div>

    <div class="card card-outline card-danger">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-door-open mr-2"></i>Data Izin Keluar/Pulang
                <span class="badge badge-danger ml-2">{{ $ijinList->count() }} record</span>
                @if($filterKelas)
                    <span class="badge badge-info ml-1">{{ $filterKelas }}</span>
                @endif
            </h3>
            <div class="card-tools">
                <span class="text-muted" style="font-size:11px;">
                    📅 {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered ijin-table mb-0" id="tabelIjin">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Keterangan</th>
                            <th>Jam Keluar</th>
                            <th>Jam Kembali</th>
                            <th>Status</th>
                            <th class="no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ijinList as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><code>{{ $p->nis }}</code></td>
                            <td>
                                <a href="{{ route('presensi.rekap.detail', ['nis'=>$p->nis]) }}" style="color:inherit;">
                                    <strong>{{ $p->nama }}</strong>
                                </a>
                            </td>
                            <td>{{ $p->kelas ?? '-' }}</td>
                            <td>{{ $p->info ?? '-' }}</td>
                            <td>{{ $p->jam_keluar ?? '-' }}</td>
                            <td>{{ $p->jam_kembali ?? '-' }}</td>
                            <td>
                                @if($p->jam_kembali)
                                    <span class="badge badge-sudah">✅ Kembali</span>
                                @else
                                    <span class="badge badge-belum">🚪 Diluar</span>
                                @endif
                            </td>
                            <td class="no-print">
                                <div class="d-flex" style="gap:4px;">
                                    <button class="btn btn-xs btn-warning"
                                        onclick="editIjin({{ $p->id }}, '{{ $p->jam_keluar }}', '{{ $p->jam_kembali }}', '{{ $p->info }}', '{{ $tanggal }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('presensi.ijin.destroy', $p->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus data izin {{ $p->nama }}?')">
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
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada data izin
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ijinList->count() > 0)
        <div class="card-footer" style="font-size:11px;">
            Total {{ $ijinList->count() }} | Belum Kembali: {{ $totalBelumKembali }} | Sudah Kembali: {{ $totalSudahKembali }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade no-print" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Izin Manual</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('presensi.ijin.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>NIS Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control"
                            placeholder="Ketik NIS siswa..." required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control"
                            value="{{ $tanggal }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam Keluar <span class="text-danger">*</span></label>
                                <input type="time" name="jam_keluar" class="form-control" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam Kembali</label>
                                <input type="time" name="jam_kembali" class="form-control" step="1">
                                <small class="text-muted">Kosongkan jika belum kembali</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="info" class="form-control"
                            placeholder="Perijinan Masjid / Pulang / dll..."
                            value="Manual">
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
<div class="modal fade no-print" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Izin</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam Keluar</label>
                                <input type="time" name="jam_keluar" id="edit-keluar"
                                    class="form-control" step="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam Kembali</label>
                                <input type="time" name="jam_kembali" id="edit-kembali"
                                    class="form-control" step="1">
                                <small class="text-muted">Kosongkan jika belum kembali</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="info" id="edit-info" class="form-control">
                    </div>
                    <input type="hidden" name="tanggal" id="edit-tanggal" value="{{ $tanggal }}">
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
function editIjin(id, keluar, kembali, info, tanggal) {
    document.getElementById('edit-keluar').value   = keluar || '';
    document.getElementById('edit-kembali').value  = kembali || '';
    document.getElementById('edit-info').value     = info || '';
    document.getElementById('edit-tanggal').value  = tanggal;
    document.getElementById('formEdit').action     = '/presensi/ijin/' + id;
    $('#modalEdit').modal('show');
}

document.getElementById('searchInput').addEventListener('input', function() {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#tabelIjin tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
});
</script>
@endpush