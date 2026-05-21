<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal     = $request->input('tanggal', date('Y-m-d'));
        $isToday     = ($tanggal === date('Y-m-d'));
        $jam         = date('H:i:s');
        $setting     = DB::table('statusnya')->first();
        $filterKelas = $request->input('kelas', '');
        $tab         = $request->input('tab', 'presensi');

        // ── Status presensi masuk/pulang ──
        $statusMasuk = match ((int)($setting->mode ?? 0)) {
            1 => ['label' => 'MASUK',  'color' => '#00c853', 'icon' => 'door-open',   'sub' => 'Presensi masuk sedang dibuka'],
            2 => ['label' => 'PULANG', 'color' => '#ff8800', 'icon' => 'door-closed', 'sub' => 'Presensi pulang sedang dibuka'],
            default => ['label' => 'TUTUP', 'color' => '#607d8b', 'icon' => 'ban',    'sub' => 'Presensi sedang ditutup'],
        };

        // ── Status sholat ──
        $batasDzuhurMulai   = '11:45:00';
        $batasDzuhurSelesai = '14:30:00';
        $batasAsharMulai    = '14:30:01';
        $batasAsharSelesai  = '17:00:00';

        if ($jam >= $batasDzuhurMulai && $jam <= $batasDzuhurSelesai) {
            $statusSholat = ['label' => 'DZUHUR', 'color' => '#ff8800', 'aktif' => true];
        } elseif ($jam >= $batasAsharMulai && $jam <= $batasAsharSelesai) {
            $statusSholat = ['label' => 'ASHAR',  'color' => '#9c27b0', 'aktif' => true];
        } else {
            $statusSholat = ['label' => 'TUTUP',  'color' => '#607d8b', 'aktif' => false];
        }

        // ── Stat hari ini ──
        $totalHadir  = DB::table('datapresensi')->where('tanggal', $tanggal)->count();
        $totalDzuhur = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('keterangan', 'DZUHUR')->where('ruang', '!=', 'Izin Mens')->count();
        $totalAshar  = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('keterangan', 'ASHAR')->where('ruang', '!=', 'Izin Mens')->count();
        $totalIzin   = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('ruang', 'Izin Mens')->count();

        // ── Daftar kelas ──
        $kelasList = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');

        // ── Recent presensi masuk ──
        $recentPresensi = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->when($filterKelas, fn($q) => $q->where('info', $filterKelas))
            ->orderBy('updated_at', 'desc')
            // ->limit(30)
            ->get();

        // ── Recent sholat (pivot per siswa) ──
        $recentSholat = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal)
            ->when($filterKelas, fn($q) => $q->where('ds.kelas', $filterKelas))
            ->select('pe.nis', 'ds.nama', 'ds.kelas', 'pe.keterangan', 'pe.ruang', 'pe.mulai', 'pe.timestamp')
            ->orderBy('pe.mulai', 'desc')
            ->get();

        // Pivot sholat
        $sholatMap = [];
        foreach ($recentSholat as $e) {
            $nis = $e->nis;
            if (!isset($sholatMap[$nis])) {
                $sholatMap[$nis] = [
                    'nama'        => $e->nama ?? '-',
                    'kelas'       => $e->kelas ?? '-',
                    'dzuhur'      => null,
                    'ashar'       => null,
                    'izin_mens'   => false,
                    'last_time'   => $e->mulai,
                ];
            } else {
                // Update last_time ke timestamp terbaru dari semua event siswa ini
                if ($e->timestamp > $sholatMap[$nis]['last_time']) {
                    $sholatMap[$nis]['last_time'] = $e->timestamp;
                }
            }
            if ($e->keterangan === 'DZUHUR') $sholatMap[$nis]['dzuhur'] = $e->mulai;
            if ($e->keterangan === 'ASHAR')  $sholatMap[$nis]['ashar']  = $e->mulai;
            if ($e->ruang === 'Izin Mens')   $sholatMap[$nis]['izin_mens'] = true;
        }
        $sholatList = collect(array_values($sholatMap))
            ->sortByDesc('last_time')
            ->values();

        return view('home', compact(
            'totalHadir',
            'totalDzuhur',
            'totalAshar',
            'totalIzin',
            'statusMasuk',
            'statusSholat',
            'setting',
            'kelasList',
            'filterKelas',
            'tab',
            'tanggal',
            'isToday',
            'recentPresensi',
            'sholatList'
        ));
    }

    public function poll(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal     = $request->input('tanggal', date('Y-m-d'));
        $filterKelas = $request->input('kelas', '');
        $setting     = DB::table('statusnya')->first();

        // ── Stat ──
        $totalHadir  = DB::table('datapresensi')->where('tanggal', $tanggal)->count();
        $totalDzuhur = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('keterangan', 'DZUHUR')->where('ruang', '!=', 'Izin Mens')->count();
        $totalAshar  = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('keterangan', 'ASHAR')->where('ruang', '!=', 'Izin Mens')->count();
        $totalIzin   = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('ruang', 'Izin Mens')->count();

        // ── Presensi ──
        $recentPresensi = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->when($filterKelas, fn($q) => $q->where('info', $filterKelas))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn($p) => [
                'nama'        => $p->nama,
                'nomorinduk'  => $p->nomorinduk ?? '-',
                'info'        => $p->info,
                'waktumasuk'  => $p->waktumasuk ?? '-',
                'ketmasuk'    => $p->ketmasuk ?? '-',
                'waktupulang' => ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : null,
                'ketpulang'   => $p->ketpulang ?? '-',
            ]);

        // ── Sholat ──
        $eventRaw = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal)
            ->when($filterKelas, fn($q) => $q->where('ds.kelas', $filterKelas))
            ->select('pe.nis', 'ds.nama', 'ds.kelas', 'pe.keterangan', 'pe.ruang', 'pe.mulai', 'pe.timestamp')
            ->orderBy('pe.mulai', 'desc')
            ->get();

        $sholatMap = [];
        foreach ($eventRaw as $e) {
            $nis = $e->nis;
            if (!isset($sholatMap[$nis])) {
                $sholatMap[$nis] = [
                    'nama'      => $e->nama ?? '-',
                    'kelas'     => $e->kelas ?? '-',
                    'dzuhur'    => null,
                    'ashar'     => null,
                    'izin_mens' => false,
                    'last_time' => $e->mulai,
                ];
            } else {
                if ($e->mulai > $sholatMap[$nis]['last_time']) {
                    $sholatMap[$nis]['last_time'] = $e->mulai;
                }
            }
            if ($e->ruang === 'Izin Mens') {
                $sholatMap[$nis]['izin_mens'] = true;
                $sholatMap[$nis]['dzuhur'] = $e->mulai;
            } elseif ($e->keterangan === 'DZUHUR') {
                $sholatMap[$nis]['dzuhur'] = $e->mulai;
            } elseif ($e->keterangan === 'ASHAR') {
                $sholatMap[$nis]['ashar'] = $e->mulai;
            }
        }

        // ── Rekap presensi per kelas ──
        $tingkatAktif = json_decode($setting->tingkat_aktif ?? '["X","XI","XII"]', true);

        $rekapPresensi = DB::table('datasiswa as ds')
            ->leftJoin('datapresensi as dp', function ($join) use ($tanggal) {
                $join->on('dp.nomorinduk', '=', 'ds.nis')
                    ->where('dp.tanggal', $tanggal);
            })
            ->whereIn('ds.tingkat', $tingkatAktif)
            ->selectRaw("
                ds.kelas,
                COUNT(DISTINCT ds.id) as total,
                COUNT(DISTINCT CASE WHEN dp.ketmasuk = 'M' THEN dp.id END) as tepat,
                COUNT(DISTINCT CASE WHEN dp.ketmasuk IN ('T','TLT') THEN dp.id END) as terlambat,
                COUNT(DISTINCT CASE WHEN dp.ketpulang IN ('P','PLG') THEN dp.id END) as pulang_normal,
                COUNT(DISTINCT CASE WHEN dp.ketpulang = 'C'  THEN dp.id END) as pulang_cepat,
                COUNT(DISTINCT CASE WHEN dp.ketmasuk IS NOT NULL AND dp.ketmasuk != '-' AND (dp.ketpulang IS NULL OR dp.ketpulang IN ('0','-')) THEN dp.id END) as belum_pulang
            ")
            ->groupBy('ds.kelas')
            ->orderBy('ds.kelas')
            ->get();

        // ── Rekap sholat per kelas ──
        $rekapSholat = DB::table('datasiswa as ds')
            ->leftJoin('presensiEvent as pe', function ($join) use ($tanggal) {
                $join->on('pe.nis', '=', 'ds.nis')
                    ->where('pe.tanggal', $tanggal);
            })
            ->whereIn('ds.tingkat', $tingkatAktif)
            ->selectRaw("
                ds.kelas,
                COUNT(DISTINCT ds.id) as total,
                COUNT(DISTINCT CASE WHEN pe.keterangan='DZUHUR' AND pe.ruang != 'Izin Mens' THEN pe.nis END) as dzuhur,
                COUNT(DISTINCT CASE WHEN pe.keterangan='ASHAR'  AND pe.ruang != 'Izin Mens' THEN pe.nis END) as ashar,
                COUNT(DISTINCT CASE WHEN pe.ruang = 'Izin Mens' THEN pe.nis END) as izin
            ")
            ->groupBy('ds.kelas')
            ->orderBy('ds.kelas')
            ->get()
            ->map(function ($row) {
                $row->keduanya = min($row->dzuhur, $row->ashar);
                $row->alpa     = max(0, $row->total - $row->dzuhur - $row->ashar - $row->izin);
                return $row;
            });

        // ── Chart sholat 14 hari ──
        $chartSholat = DB::table('presensiEvent')
            ->selectRaw("tanggal,
        SUM(CASE WHEN keterangan='DZUHUR' AND ruang != 'Izin Mens' THEN 1 ELSE 0 END) as dzuhur,
        SUM(CASE WHEN keterangan='ASHAR'  AND ruang != 'Izin Mens' THEN 1 ELSE 0 END) as ashar,
        SUM(CASE WHEN ruang='Izin Mens' THEN 1 ELSE 0 END) as izin")
            ->where('tanggal', '>=', date('Y-m-d', strtotime('-14 days')))
            ->where('tanggal', '<=', $tanggal)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // ── Chart presensi 14 hari ──
        $chartPresensi = DB::table('datapresensi')
            ->selectRaw("tanggal,
        SUM(CASE WHEN ketmasuk = 'M'   THEN 1 ELSE 0 END) as tepat,
        SUM(CASE WHEN ketmasuk = 'T'   THEN 1 ELSE 0 END) as toleransi,
        SUM(CASE WHEN ketmasuk = 'TLT' THEN 1 ELSE 0 END) as terlambat")
            ->where('tanggal', '>=', date('Y-m-d', strtotime('-14 days')))
            ->where('tanggal', '<=', $tanggal)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return response()->json([
            'stat' => [
                'hadir'  => $totalHadir,
                'dzuhur' => $totalDzuhur,
                'ashar'  => $totalAshar,
                'izin'   => $totalIzin,
            ],
            'presensi'      => $recentPresensi->values(),
            'sholat'        => collect(array_values($sholatMap))->sortByDesc('last_time')->values(),
            'rekapPresensi' => $rekapPresensi->values(),
            'rekapSholat'   => $rekapSholat->values(),
            'chartSholat'   => $chartSholat->values(),
            'chartPresensi' => $chartPresensi->values(),
        ]);
    }
}
