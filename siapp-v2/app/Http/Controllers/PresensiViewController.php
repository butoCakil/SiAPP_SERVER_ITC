<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiViewController extends Controller
{
    // ══════════════════════════════════════════
    // PRESENSI HARIAN
    // ══════════════════════════════════════════

    public function index(Request $request)
    {
        $tanggal   = $request->input('tanggal', date('Y-m-d'));
        $kelas     = $request->input('kelas', '');
        $filterKet = $request->input('ket', '');

        $setting      = DB::table('statusnya')->first();
        $tingkatAktif = json_decode($setting->tingkat_aktif ?? '["X","XI","XII"]', true);

        $query = DB::table('datapresensi as dp')
            ->leftJoin('datasiswa as ds', 'ds.nokartu', '=', 'dp.nokartu')
            ->where('dp.tanggal', $tanggal)
            ->whereIn('ds.tingkat', $tingkatAktif)
            ->orderBy('dp.waktumasuk')
            ->select('dp.*');

        if ($kelas) $query->where('dp.info', $kelas);

        if ($filterKet === 'terlambat')       $query->whereIn('dp.ketmasuk', ['T','TL','TLT']);
        elseif ($filterKet === 'tepat')       $query->whereIn('dp.ketmasuk', ['M','TW']);
        elseif ($filterKet === 'pulang_awal') $query->where('dp.ketpulang', 'PA');

        $presensi    = $query->get();
        $total       = $presensi->count();
        $tepat       = $presensi->where('ketmasuk', 'TW')->count();
        $telat       = $presensi->whereIn('ketmasuk', ['TL', 'TLT', 'T'])->count();
        $sudahPulang = $presensi->filter(fn($p) => $p->waktupulang && $p->waktupulang !== '00:00:00')->count();

        $kelasList = DB::table('datapresensi')
            ->where('tanggal', $tanggal)->whereNotNull('info')
            ->distinct()->orderBy('info')->pluck('info');

        return view('presensi.index', compact(
            'presensi', 'tanggal', 'total', 'tepat', 'telat', 'sudahPulang',
            'kelasList', 'kelas', 'filterKet'
        ));
    }

    public function create()
    {
        $siswaList = DB::table('datasiswa')->orderBy('kelas')->orderBy('nama')->get();
        return view('presensi.create', compact('siswaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nokartu'    => 'required',
            'nomorinduk' => 'required',
            'nama'       => 'required',
            'tanggal'    => 'required|date',
            'waktumasuk' => 'required',
        ]);

        // Cek duplikat
        $ada = DB::table('datapresensi')
            ->where('nokartu', $request->nokartu)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($ada) {
            return back()->with('error', 'Data presensi sudah ada untuk siswa ini pada tanggal tersebut.')->withInput();
        }

        DB::table('datapresensi')->insert([
            'nokartu'    => strtoupper($request->nokartu),
            'nomorinduk' => $request->nomorinduk,
            'nama'       => $request->nama,
            'info'       => $request->info ?? '',
            'tanggal'    => $request->tanggal,
            'waktumasuk' => $request->waktumasuk,
            'ketmasuk'   => $request->ketmasuk ?? 'TW',
            'waktupulang'=> $request->waktupulang ?? null,
            'ketpulang'  => $request->ketpulang ?? null,
            'keterangan' => $request->keterangan ?? null,
            'infodevice2'=> 'Manual',
            'updated_at' => now(),
        ]);

        return redirect()->route('presensi', ['tanggal' => $request->tanggal])
            ->with('success', 'Data presensi berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $presensi = DB::table('datapresensi')->where('id', $id)->first();
        if (!$presensi) abort(404);
        return view('presensi.edit', compact('presensi'));
    }

    public function update(Request $request, int $id)
    {
        DB::table('datapresensi')->where('id', $id)->update([
            'waktumasuk'  => $request->waktumasuk,
            'ketmasuk'    => $request->ketmasuk,
            'waktupulang' => $request->waktupulang ?: null,
            'ketpulang'   => $request->ketpulang ?: null,
            'keterangan'  => $request->keterangan ?: null,
            'updated_at'  => now(),
        ]);

        return redirect()->route('presensi', ['tanggal' => $request->tanggal])
            ->with('success', 'Data presensi berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $p = DB::table('datapresensi')->where('id', $id)->first();
        DB::table('datapresensi')->where('id', $id)->delete();
        return redirect()->route('presensi', ['tanggal' => $p->tanggal ?? date('Y-m-d')])
            ->with('success', 'Data presensi berhasil dihapus.');
    }

    // ══════════════════════════════════════════
    // PEMBIASAAN SHOLAT
    // ══════════════════════════════════════════

    public function event(Request $request)
    {
        $tanggal     = $request->input('tanggal', date('Y-m-d'));
        $filterKelas = $request->input('kelas', '');

        $query = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal);

        if ($filterKelas) $query->where('ds.kelas', $filterKelas);

        $events = $query->select(
                'pe.id', 'pe.nis', 'ds.nama', 'ds.kelas',
                'pe.keterangan', 'pe.ruang', 'pe.mulai', 'pe.jam',
            )
            ->orderBy('pe.nis')->orderBy('pe.jam')->get();

        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'nis'         => $nis,
                    'nama'        => $e->nama ?? '-',
                    'kelas'       => $e->kelas ?? '-',
                    'dzuhur'      => null, 'dzuhur_id' => null,
                    'ashar'       => null, 'ashar_id'  => null,
                    'dzuhur_izin' => false,
                    'ashar_izin'  => false,
                ];
            }
            if ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['dzuhur']      = $e->mulai;
                $siswaMap[$nis]['dzuhur_id']   = $e->id;
                $siswaMap[$nis]['dzuhur_izin'] = $e->ruang === 'Izin Mens';
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['ashar']      = $e->mulai;
                $siswaMap[$nis]['ashar_id']   = $e->id;
                $siswaMap[$nis]['ashar_izin'] = $e->ruang === 'Izin Mens';
            }
        }

        $siswaList          = collect(array_values($siswaMap));
        $totalDzuhur        = $siswaList->filter(fn($s) => $s['dzuhur'])->count();
        $totalAshar         = $siswaList->filter(fn($s) => $s['ashar'])->count();
        $totalIzin          = $siswaList->filter(fn($s) => $s['dzuhur_izin'] || $s['ashar_izin'])->count();
        $totalKeduanya      = $siswaList->filter(fn($s) => $s['dzuhur'] && $s['ashar'])->count();
        $totalTidakKeduanya = $siswaList->filter(fn($s) => !$s['dzuhur'] || !$s['ashar'])->count();
        $kelasList          = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('presensi.event', compact(
            'siswaList', 'tanggal', 'filterKelas', 'kelasList',
            'totalDzuhur', 'totalAshar', 'totalIzin',
            'totalKeduanya', 'totalTidakKeduanya'
        ));
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'nis'         => 'required',
            'tanggal'     => 'required|date',
            'keterangan'  => 'required|in:DZUHUR,ASHAR',
            'jam'         => 'required',
        ]);

        $siswa = DB::table('datasiswa')->where('nis', $request->nis)->first();
        if (!$siswa) return back()->with('error', 'Siswa tidak ditemukan.');

        $ada = DB::table('presensiEvent')
            ->where('nis', $request->nis)
            ->where('tanggal', $request->tanggal)
            ->where('keterangan', $request->keterangan)
            ->exists();

        if ($ada) return back()->with('error', 'Data sudah ada untuk sholat ini.');

        DB::table('presensiEvent')->insert([
            'nokartu'    => $siswa->nokartu,
            'nis'        => $request->nis,
            'ruang'      => $request->ruang ?? 'Manual',
            'mulai'      => $request->jam,
            'jam'        => $request->jam,
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan,
            'timestamp'  => now(),
        ]);

        return redirect()->route('presensi.event', ['tanggal' => $request->tanggal])
            ->with('success', 'Data sholat berhasil ditambahkan.');
    }

    public function updateEvent(Request $request, int $id)
    {
        DB::table('presensiEvent')->where('id', $id)->update([
            'jam'        => $request->jam,
            'mulai'      => $request->jam,
            'keterangan' => $request->keterangan,
            'ruang'      => $request->ruang,
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function destroyEvent(int $id)
    {
        $e = DB::table('presensiEvent')->where('id', $id)->first();
        DB::table('presensiEvent')->where('id', $id)->delete();
        return redirect()->route('presensi.event', ['tanggal' => $e->tanggal ?? date('Y-m-d')])
            ->with('success', 'Data sholat berhasil dihapus.');
    }
}