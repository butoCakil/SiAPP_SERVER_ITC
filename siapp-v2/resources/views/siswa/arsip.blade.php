@extends('layouts.app')

@section('title', 'Arsip Siswa')
@section('page_title', 'Arsip Siswa (Lulus/Keluar)')

@push('styles')
<style>
.toolbar {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 14px;
}
.search-box {
    display: flex;
    align-items: center;
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 0 12px;
    flex: 1;
    min-width: 200px;
    max-width: 320px;
    transition: border-color 0.2s;
}
.search-box:focus-within { border-color: #007bff; }
.search-box i { color: #aaa; margin-right: 8px; }
.search-box input {
    border: none;
    outline: none;
    padding: 8px 0;
    font-size: 13px;
    width: 100%;
    background: transparent;
}
.kelas-select {
    padding: 8px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #fff;
    cursor: pointer;
    transition: border-color 0.2s;
}
.kelas-select:focus { border-color: #007bff; }
.count-badge {
    margin-left: auto;
    background: #6c757d;
    color: #fff;
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.table-siswa { font-size: 12px; }
.table-siswa th { font-size: 11px; font-weight: 700; white-space: nowrap; }
.table-siswa td { vertical-align: middle; }
.badge-status {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 12px;
    font-weight: 600;
}
.badge-lulus  { background: #d4edda; color: #155724; }
.badge-keluar { background: #f8d7da; color: #721c24; }
.hidden-row { display: none !important; }
</style>
@endpush

@section('content')

<div class="mb-2">
    <a href="{{ route('siswa') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Data Siswa
    </a>
</div>

{{-- Toolbar --}}
<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari nama, NIS..." autofocus>
    </div>
    <select class="kelas-select" id="kelasFilter">
        <option value="">Semua Kelas</option>
        @foreach($kelasList as $k)
            <option value="{{ $k }}">{{ $k }}</option>
        @endforeach
    </select>
    <select class="kelas-select" id="statusFilter">
        <option value="">Semua Status</option>
        <option value="lulus">Lulus</option>
        <option value="keluar">Keluar</option>
    </select>
    <span class="count-badge" id="count-badge">{{ $siswa->count() }} siswa</span>
</div>

{{-- Tabel --}}
<div class="card card-outline card-secondary">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-siswa mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="width:80px">NIS</th>
                        <th>Nama</th>
                        <th style="width:140px">Kelas Terakhir</th>
                        <th style="width:100px">Status</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-siswa">
                    @foreach($siswa as $i => $s)
                    <tr id="row-{{ $s->id }}"
                        data-nama="{{ strtolower($s->nama) }}"
                        data-nis="{{ $s->nis }}"
                        data-kelas="{{ $s->kelas }}"
                        data-status="{{ $s->status }}">
                        <td class="row-num">{{ $i + 1 }}</td>
                        <td><code style="color:#e83e8c;">{{ $s->nis }}</code></td>
                        <td><strong>{{ $s->nama }}</strong></td>
                        <td><small>{{ $s->kelas }}</small></td>
                        <td>
                            <span class="badge-status {{ $s->status === 'lulus' ? 'badge-lulus' : 'badge-keluar' }}">
                                {{ ucfirst($s->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex" style="gap:3px;">
                                <a href="{{ route('siswa.edit', $s->id) }}"
                                    class="btn btn-xs btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('siswa.pulihkan', $s->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Pulihkan {{ addslashes($s->nama) }} ke status aktif?')">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success" title="Pulihkan ke Aktif">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted" style="font-size:11px;" id="footer-info">
        Total {{ $siswa->count() }} siswa
    </div>
</div>

@endsection

@push('scripts')
<script>
const rows    = Array.from(document.querySelectorAll('#tbody-siswa tr'));
const counter = document.getElementById('count-badge');
const footer  = document.getElementById('footer-info');

function applyFilter() {
    const q      = document.getElementById('searchInput').value.toLowerCase().trim();
    const kelas  = document.getElementById('kelasFilter').value;
    const status = document.getElementById('statusFilter').value;
    let   count = 0, num = 1;

    rows.forEach(row => {
        const matchSearch = !q ||
            row.dataset.nama.includes(q) ||
            row.dataset.nis.includes(q);

        const matchKelas  = !kelas  || row.dataset.kelas  === kelas;
        const matchStatus = !status || row.dataset.status === status;

        if (matchSearch && matchKelas && matchStatus) {
            row.classList.remove('hidden-row');
            row.querySelector('.row-num').textContent = num++;
            count++;
        } else {
            row.classList.add('hidden-row');
        }
    });

    counter.textContent = count + ' siswa';
    footer.textContent  = `Menampilkan ${count} dari ${rows.length} siswa`;
}

document.getElementById('searchInput').addEventListener('input',  applyFilter);
document.getElementById('kelasFilter').addEventListener('change', applyFilter);
document.getElementById('statusFilter').addEventListener('change', applyFilter);
</script>
@endpush
