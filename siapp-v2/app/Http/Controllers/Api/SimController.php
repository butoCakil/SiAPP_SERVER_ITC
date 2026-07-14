<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimController extends Controller
{
    // ── 1. Presensi Harian ──
    public function presensi(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelas   = $request->input('kelas', '');

        $query = DB::table('datapresensi as dp')
            ->leftJoin('datasiswa as ds', 'ds.nokartu', '=', 'dp.nokartu')
            ->where('dp.tanggal', $tanggal)
            ->orderBy('dp.waktumasuk');

        if ($kelas) $query->where('dp.info', $kelas);

        $data = $query->select(
            'dp.nomorinduk as nis',
            'dp.nama',
            'dp.info as kelas',
            'dp.waktumasuk',
            'dp.ketmasuk',
            'dp.waktupulang',
            'dp.ketpulang',
            'dp.infodevice2 as device'
        )->get()
            ->map(fn($p) => [
                'nis'          => $p->nis,
                'nama'         => $p->nama,
                'kelas'        => $p->kelas,
                'waktu_masuk'  => $p->waktumasuk,
                'ket_masuk'    => $p->ketmasuk,
                'waktu_pulang' => ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : null,
                'ket_pulang'   => $p->ketpulang ?: null,
                'device'       => $p->device,
            ]);

        return response()->json([
            'type'      => 'presensi_harian',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $tanggal,
            'total'     => $data->count(),
            'data'      => $data->values(),
        ]);
    }

    // ── 2. Presensi Harian Range ──
    public function presensiRange(Request $request)
    {
        $dari   = $request->input('dari', date('Y-m-d'));
        $sampai = $request->input('sampai', date('Y-m-d'));

        $data = DB::table('datapresensi as dp')
            ->leftJoin('datasiswa as ds', 'ds.nokartu', '=', 'dp.nokartu')
            ->whereBetween('dp.tanggal', [$dari, $sampai])
            ->orderBy('dp.tanggal')->orderBy('dp.waktumasuk')
            ->select(
                'dp.tanggal',
                'dp.nomorinduk as nis',
                'dp.nama',
                'dp.info as kelas',
                'dp.waktumasuk',
                'dp.ketmasuk',
                'dp.waktupulang',
                'dp.ketpulang',
                'dp.infodevice2 as device'
            )->get()
            ->map(fn($p) => [
                'tanggal'      => $p->tanggal,
                'nis'          => $p->nis,
                'nama'         => $p->nama,
                'kelas'        => $p->kelas,
                'waktu_masuk'  => $p->waktumasuk,
                'ket_masuk'    => $p->ketmasuk,
                'waktu_pulang' => ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : null,
                'ket_pulang'   => $p->ketpulang ?: null,
                'device'       => $p->device,
            ]);

        return response()->json([
            'type'      => 'presensi_harian',
            'timestamp' => now()->toIso8601String(),
            'dari'      => $dari,
            'sampai'    => $sampai,
            'total'     => $data->count(),
            'data'      => $data->values(),
        ]);
    }

    // ── 3. Sholat ──
    public function sholat(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelas   = $request->input('kelas', '');

        $query = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal)
            ->where('pe.ruang', '!=', 'Izin Mens');

        if ($kelas) $query->where('ds.kelas', $kelas);

        $events = $query->select(
            'pe.nis',
            'ds.nama',
            'ds.kelas',
            'pe.keterangan',
            'pe.ruang',
            'pe.mulai'
        )->get();

        // Pivot per siswa
        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'nis'           => $nis,
                    'nama'          => $e->nama ?? '-',
                    'kelas'         => $e->kelas ?? '-',
                    'dhuha'         => null,
                    'dzuhur'        => null,
                    'ashar'         => null,
                    'device_dhuha'  => null,
                    'device_dzuhur' => null,
                    'device_ashar'  => null,
                ];
            }
            if ($e->keterangan === 'DHUHA') {
                $siswaMap[$nis]['dhuha']        = $e->mulai;
                $siswaMap[$nis]['device_dhuha'] = $e->ruang;
            } elseif ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['dzuhur']        = $e->mulai;
                $siswaMap[$nis]['device_dzuhur'] = $e->ruang;
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['ashar']        = $e->mulai;
                $siswaMap[$nis]['device_ashar'] = $e->ruang;
            }
        }

        return response()->json([
            'type'      => 'presensi_sholat',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $tanggal,
            'total'     => count($siswaMap),
            'data'      => array_values($siswaMap),
        ]);
    }

    // ── 4. Izin Menstruasi ──
    public function izinMens(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelas   = $request->input('kelas', '');

        $query = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal)
            ->where('pe.ruang', 'Izin Mens');

        if ($kelas) $query->where('ds.kelas', $kelas);

        $events = $query->select(
            'pe.nis',
            'ds.nama',
            'ds.kelas',
            'pe.mulai',
            'pe.keterangan'
        )->get();

        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'nis'          => $nis,
                    'nama'         => $e->nama ?? '-',
                    'kelas'        => $e->kelas ?? '-',
                    'waktu_dzuhur' => null,
                    'waktu_ashar'  => null,
                ];
            }
            if ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['waktu_dzuhur'] = $e->mulai;
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['waktu_ashar'] = $e->mulai;
            }
        }

        return response()->json([
            'type'      => 'izin_mens',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $tanggal,
            'total'     => count($siswaMap),
            'data'      => array_values($siswaMap),
        ]);
    }

    // ── 5. Izin Keluar/Pulang ──
    public function ijin(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelas   = $request->input('kelas', '');
        $status  = $request->input('status', ''); // belum / sudah

        $query = DB::table('daftarijin as di')
            ->leftJoin('datasiswa as ds', DB::raw('ds.nis COLLATE utf8_general_ci'), '=', DB::raw('di.nis COLLATE utf8_general_ci'))
            ->where('di.tanggalijin', $tanggal);

        if ($kelas)           $query->where('ds.kelas', $kelas);
        if ($status === 'belum') $query->whereNull('di.jam_kembali');
        if ($status === 'sudah') $query->whereNotNull('di.jam_kembali');

        $data = $query->select(
            'di.nis',
            'di.nama',
            'ds.kelas',
            'di.jam_keluar',
            'di.jam_kembali',
            'di.info'
        )->get()
            ->map(fn($p) => [
                'nis'         => $p->nis,
                'nama'        => $p->nama,
                'kelas'       => $p->kelas ?? '-',
                'jam_keluar'  => $p->jam_keluar,
                'jam_kembali' => $p->jam_kembali,
                'keterangan'  => $p->info,
                'status'      => $p->jam_kembali ? 'kembali' : 'belum_kembali',
            ]);

        return response()->json([
            'type'      => 'izin_keluar',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $tanggal,
            'total'     => $data->count(),
            'data'      => $data->values(),
        ]);
    }

    // ── 6. Data Siswa ──
    public function siswa(Request $request)
    {
        $kelas = $request->input('kelas', '');

        $query = DB::table('datasiswa')
            ->where('status', 'aktif')
            ->select('nis', 'nama', 'nick', 'kelas', 'tingkat', 'jur', 'kelompok', 'foto', 'keterangan');

        if ($kelas) $query->where('kelas', $kelas);

        $data = $query->orderBy('kelas')->orderBy('nama')->get();

        return response()->json([
            'type'  => 'data_siswa',
            'timestamp' => now()->toIso8601String(),
            'total' => $data->count(),
            'data'  => $data,
        ]);
    }
}
