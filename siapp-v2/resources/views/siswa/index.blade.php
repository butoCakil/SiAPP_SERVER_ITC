@extends('layouts.app')

@section('title', 'Manajemen Siswa')
@section('page_title', 'Manajemen Siswa')

@push('styles')
<style>
/* ── Scanner Panel ── */
.scanner-panel {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 14px;
    padding: 14px 24px;
    color: #fff;
    margin-bottom: 20px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.25);
}

.scanner-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: center;
}

@media (max-width: 768px) { .scanner-grid { grid-template-columns: 1fr; } }

.scanner-label {
    font-size: 11px;
    opacity: 0.6;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.kartu-display {
    background: rgba(255,255,255,0.06);
    border: 2px dashed rgba(255,255,255,0.2);
    border-radius: 10px;
    padding: 14px 20px;
    text-align: center;
    font-size: 26px;
    font-weight: 900;
    font-family: 'Courier New', monospace;
    letter-spacing: 8px;
    color: rgba(255,255,255,0.25);
    min-height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.35s ease;
}

.kartu-display.active {
    border-color: #4CAF50;
    border-style: solid;
    background: rgba(76,175,80,0.1);
    color: #4CAF50;
    box-shadow: 0 0 20px rgba(76,175,80,0.25);
}

.kartu-display.pulse-anim { animation: blink 1.2s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

.scanner-right { 
    display: flex; 
    flex-direction: column; 
    gap: 8px;
    justify-content: center;
}

.manual-row { display:flex; gap:8px; align-items:flex-end; }

.manual-row input {
    flex: 1;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 8px 12px;
    color: #fff;
    font-family: monospace;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 3px;
    outline: none;
}
.manual-row input::placeholder { color: rgba(255,255,255,0.3); letter-spacing:1px; font-weight:400; }
.manual-row input:focus { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.12); }

.btn-set {
    padding: 8px 16px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
}
.btn-set:hover { background: rgba(255,255,255,0.25); }

.btn-clear {
    padding: 8px 12px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    color: rgba(255,255,255,0.6);
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.btn-clear:hover { border-color: #ff5252; color: #ff5252; }

.status-msg { font-size: 12px; min-height: 20px; }
.status-ok   { color: #4CAF50; }
.status-err  { color: #ff5252; }
.status-warn { color: #FFC107; }

/* ── Toolbar ── */
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
    background: #007bff;
    color: #fff;
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

/* ── Table ── */
.table-siswa { font-size: 12px; }
.table-siswa th { font-size: 11px; font-weight: 700; white-space: nowrap; }
.table-siswa td { vertical-align: middle; }

.nokartu-code {
    font-family: monospace;
    font-size: 12px;
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 2px 7px;
    color: #333;
}
.nokartu-code.empty { color: #ccc; background: #fafafa; }

.btn-assign {
    border: none;
    border-radius: 6px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.btn-assign.ready {
    background: linear-gradient(135deg,#4CAF50,#388E3C);
    color: #fff;
    box-shadow: 0 2px 8px rgba(76,175,80,0.3);
}
.btn-assign.ready:hover { transform: translateY(-1px); filter: brightness(1.1); }
.btn-assign.nocard { background: #f0f0f0; color: #bbb; cursor: not-allowed; }

.hidden-row { display: none !important; }
</style>
@endpush

@section('content')

{{-- Scanner Panel --}}
<div class="scanner-panel">
    <div class="scanner-grid">

        {{-- Kiri: Display kartu --}}
        <div>
            <div class="scanner-label">📡 Kartu RFID Terdeteksi</div>
            <div class="kartu-display pulse-anim" id="kartu-display">
                Menunggu kartu...
            </div>
        </div>

        {{-- Kanan: Input manual + status --}}
        <div class="scanner-right">
            <div>
                <div class="scanner-label">⌨️ Input Manual</div>
                <div class="manual-row">
                    <input type="text" id="kartu-manual"
                        placeholder="8 karakter hex..."
                        maxlength="8"
                        oninput="this.value=this.value.toUpperCase()"
                        onkeydown="if(event.key==='Enter') setManual()">
                    <button class="btn-set" onclick="setManual()">Set</button>
                    <button class="btn-clear" onclick="clearKartu()">✕</button>
                </div>
            </div>
            <div id="assign-status" class="status-msg"></div>
        </div>

    </div>
</div>

{{-- Toolbar --}}
<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari nama, NIS, atau nomor kartu..." autofocus>
    </div>
    <select class="kelas-select" id="kelasFilter">
        <option value="">Semua Kelas</option>
        @foreach($kelasList as $k)
            <option value="{{ $k }}">{{ $k }}</option>
        @endforeach
    </select>
    <span class="count-badge" id="count-badge">{{ $siswa->count() }} siswa</span>
    <a href="{{ route('siswa.create') }}" class="btn btn-sm btn-success">
        <i class="fas fa-user-plus mr-1"></i>Tambah Siswa
    </a>
</div>

{{-- Tabel --}}
<div class="card card-outline card-primary">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-siswa mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="width:80px">NIS</th>
                        <th>Nama</th>
                        <th style="width:140px">Kelas</th>
                        <th style="width:120px">No Kartu</th>
                        <th style="width:90px">Assign</th>
                        <th style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-siswa">
                    @foreach($siswa as $i => $s)
                    <tr id="row-{{ $s->id }}"
                        data-nama="{{ strtolower($s->nama) }}"
                        data-nis="{{ $s->nis }}"
                        data-kartu="{{ strtolower($s->nokartu ?? '') }}"
                        data-kelas="{{ $s->kelas }}">
                        <td class="row-num">{{ $i + 1 }}</td>
                        <td><code style="color:#e83e8c;">{{ $s->nis }}</code></td>
                        <td><strong>{{ $s->nama }}</strong></td>
                        <td><small>{{ $s->kelas }}</small></td>
                        <td>
                            <span class="nokartu-code {{ $s->nokartu ? '' : 'empty' }}"
                                id="kartu-{{ $s->id }}">
                                {{ $s->nokartu ?: '— —' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn-assign nocard"
                                id="btn-{{ $s->id }}"
                                data-id="{{ $s->id }}"
                                data-nama="{{ $s->nama }}"
                                onclick="assignKartu({{ $s->id }}, '{{ addslashes($s->nama) }}')">
                                <i class="fas fa-id-card mr-1"></i>Assign
                            </button>
                        </td>
                    <td>
                            <div class="d-flex" style="gap:3px;">
                                <a href="{{ route('siswa.edit', $s->id) }}"
                                    class="btn btn-xs btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('siswa.destroy', $s->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus siswa {{ addslashes($s->nama) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
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
let currentKartu = '';
const CSRF = document.querySelector('meta[name=csrf-token]').content;

// ══════════════════════════════════════════
// SCANNER
// ══════════════════════════════════════════
function setKartu(nokartu) {
    currentKartu = nokartu;
    const display = document.getElementById('kartu-display');

    if (nokartu) {
        display.textContent = nokartu;
        display.classList.add('active');
        display.classList.remove('pulse-anim');
    } else {
        display.textContent = 'Menunggu kartu...';
        display.classList.remove('active');
        display.classList.add('pulse-anim');
    }

    // Toggle semua tombol assign
    document.querySelectorAll('.btn-assign').forEach(btn => {
        btn.classList.toggle('ready', !!nokartu);
        btn.classList.toggle('nocard', !nokartu);
    });
}

function setManual() {
    const val = document.getElementById('kartu-manual').value.toUpperCase().trim();
    if (val.length === 8 && /^[A-F0-9]{8}$/.test(val)) {
        setKartu(val);
        showStatus('Kartu manual diset: ' + val, 'ok');
    } else {
        showStatus('Harus 8 karakter hex (0-9, A-F)', 'err');
    }
}

function clearKartu() {
    setKartu('');
    document.getElementById('kartu-manual').value = '';
    document.getElementById('assign-status').textContent = '';
}

function showStatus(msg, type) {
    const el = document.getElementById('assign-status');
    el.textContent = msg;
    el.className = 'status-msg status-' + type;
    setTimeout(() => { el.textContent = ''; el.className = 'status-msg'; }, 4000);
}

// ══════════════════════════════════════════
// ASSIGN KARTU
// ══════════════════════════════════════════
async function assignKartu(id, nama) {
    if (!currentKartu) { showStatus('Belum ada kartu!', 'warn'); return; }
    if (!confirm(`Assign kartu "${currentKartu}" ke ${nama}?`)) return;

    const btn = document.getElementById('btn-' + id);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const res  = await fetch('{{ route("siswa.kartu") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id, nokartu: currentKartu, db: 'datasiswa' })
        });
        const data = await res.json();

        if (data.status === 'ok') {
            const kartuEl = document.getElementById('kartu-' + id);
            kartuEl.textContent = currentKartu;
            kartuEl.classList.remove('empty');

            // Update data-kartu untuk search
            document.getElementById('row-' + id).dataset.kartu = currentKartu.toLowerCase();

            showStatus('✅ Berhasil assign ke ' + nama, 'ok');
            clearKartu();
        } else {
            showStatus('❌ ' + data.message, 'err');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-id-card mr-1"></i>Assign';
        }
    } catch(e) {
        showStatus('❌ Error: ' + e.message, 'err');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-id-card mr-1"></i>Assign';
    }
}

// ══════════════════════════════════════════
// SEARCH & FILTER (client-side)
// ══════════════════════════════════════════
const rows    = Array.from(document.querySelectorAll('#tbody-siswa tr'));
const counter = document.getElementById('count-badge');
const footer  = document.getElementById('footer-info');

function applyFilter() {
    const q     = document.getElementById('searchInput').value.toLowerCase().trim();
    const kelas = document.getElementById('kelasFilter').value;
    let   count = 0, num = 1;

    rows.forEach(row => {
        const matchSearch = !q ||
            row.dataset.nama.includes(q) ||
            row.dataset.nis.includes(q) ||
            row.dataset.kartu.includes(q);

        const matchKelas = !kelas || row.dataset.kelas === kelas;

        if (matchSearch && matchKelas) {
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

// ══════════════════════════════════════════
// POLLING TMPRFID
// ══════════════════════════════════════════
let lastKartu = '';
async function pollKartu() {
    try {
        const res  = await fetch('{{ route("siswa.tmprfid") }}');
        const data = await res.json();
        if (data.nokartu && data.nokartu !== lastKartu) {
            lastKartu = data.nokartu;
            setKartu(data.nokartu);
            showStatus('📡 Kartu terdeteksi: ' + data.nokartu, 'ok');
        }
    } catch {}
}

setInterval(pollKartu, 1000);
pollKartu();
</script>
@endpush