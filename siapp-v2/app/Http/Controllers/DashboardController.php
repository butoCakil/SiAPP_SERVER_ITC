<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tanggal  = $request->input('tanggal', date('Y-m-d'));
        $jam      = date('H:i:s');
        $isToday  = $tanggal === date('Y-m-d');

        // ── Presensi harian ──
        $totalHadir  = DB::table('datapresensi')->where('tanggal', $tanggal)->count();
        $totalTepat  = DB::table('datapresensi')->where('tanggal', $tanggal)->where('ketmasuk', 'TW')->count();
        $totalTelat  = DB::table('datapresensi')->where('tanggal', $tanggal)->whereIn('ketmasuk', ['TL', 'TLT'])->count();
        $totalPulang = DB::table('datapresensi')->where('tanggal', $tanggal)->whereNotNull('waktupulang')->where('waktupulang', '!=', '00:00:00')->count();

        // ── Device ──
        $totalDevice  = DB::table('devices')->where('hidden', 0)->count();
        $deviceOnline = DB::table('devices')->where('hidden', 0)->where('online', 1)->count();

        // ── Setting ──
        $setting = DB::table('statusnya')->first();

        $statusMasuk = match ((int)($setting->mode ?? 0)) {
            1 => ['label' => 'MASUK AKTIF',  'color' => 'success',   'icon' => 'door-open'],
            2 => ['label' => 'PULANG AKTIF', 'color' => 'warning',   'icon' => 'door-closed'],
            default => ['label' => 'DITUTUP', 'color' => 'secondary', 'icon' => 'ban'],
        };

        // ── Cek Kaldik ──
        $tanggal = date('Y-m-d');
        $kaldikHariIni = DB::table('kaldik')
            ->where('tanggal', $tanggal)
            ->whereIn('tipe', ['libur_nasional', 'cuti_bersama', 'libur_semester', 'daring', 'force_majeure'])
            ->first();
        if ($kaldikHariIni) {
            $tipeLabel = match ($kaldikHariIni->tipe) {
                'libur_nasional'  => '🔴 Libur Nasional',
                'cuti_bersama'    => '🟠 Cuti Bersama',
                'libur_semester'  => '🟣 Libur Semester',
                'daring'          => '🔵 Pembelajaran Daring',
                'force_majeure'   => '⚫ Force Majeure',
                default           => 'Libur',
            };
            $statusMasuk['kaldik'] = $tipeLabel . ': ' . $kaldikHariIni->judul;
        }

        // ── Status sholat ──
        $batasDhuhaStart  = $setting->dhuha_start  ?? '07:00:00';
        $batasDhuhaEnd    = $setting->dhuha_end    ?? '11:00:00';
        $batasDzuhurStart = $setting->dzuhur_start ?? '11:30:00';
        $batasDzuhurEnd   = $setting->dzuhur_end   ?? '13:30:00';
        $batasAsharStart  = $setting->ashar_start  ?? '15:00:00';
        $batasAsharEnd    = $setting->ashar_end    ?? '16:30:00';

        if ($jam >= $batasDzuhurStart && $jam <= $batasDzuhurEnd) {
            $statusSholat = ['label' => 'DZUHUR AKTIF', 'color' => 'warning',   'bg' => '#ff8800'];
        } elseif ($jam >= $batasAsharStart && $jam <= $batasAsharEnd) {
            $statusSholat = ['label' => 'ASHAR AKTIF',  'color' => 'secondary', 'bg' => '#9c27b0'];
        } elseif ($jam >= $batasDhuhaStart && $jam <= $batasDhuhaEnd) {
            $statusSholat = ['label' => 'DHUHA AKTIF',  'color' => 'info',      'bg' => '#2196f3'];
        } else {
            $statusSholat = ['label' => 'DILUAR WAKTU', 'color' => 'secondary', 'bg' => '#9e9e9e'];
        }

        // ── Sholat hari ini ──
        $totalDzuhur = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('keterangan', 'DZUHUR')->count();
        $totalAshar  = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('keterangan', 'ASHAR')->count();
        $totalIzin   = DB::table('presensiEvent')->where('tanggal', $tanggal)->where('ruang', 'Izin Mens')->count();

        // ── Recent presensi masuk ──
        $recentMasuk = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'nama'   => $p->nama,
                'info'   => $p->info,
                'waktu'  => $p->waktumasuk,
                'tipe'   => 'masuk',
                'ket'    => $p->ketmasuk,
                'device' => $p->infodevice2,
                'time'   => $p->updated_at,
            ]);

        // ── Recent sholat ──
        $recentSholat = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal)
            ->orderBy('pe.timestamp', 'desc')
            ->limit(8)
            ->select(
                'ds.nama',
                'ds.kelas as info',
                'pe.jam as waktu',
                'pe.keterangan as ket',
                'pe.ruang as device',
                'pe.timestamp as time'
            )
            ->get()
            ->map(fn($p) => [
                'nama'   => $p->nama ?? '-',
                'info'   => $p->info ?? '-',
                'waktu'  => $p->waktu,
                'tipe'   => 'sholat',
                'ket'    => $p->ket,
                'device' => $p->device,
                'time'   => $p->time,
            ]);

        $recentAll = $recentMasuk->concat($recentSholat)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        // ── Chart data sholat 14 hari ──
        // ── Rekap sholat per kelas hari ini ──
        $tingkatAktif = json_decode($setting->tingkat_aktif ?? '["X","XI","XII"]', true);
        $rekapKelas = DB::table('datasiswa as ds')
            ->leftJoin('presensiEvent as pe', function ($join) use ($tanggal) {
                $join->on('pe.nis', '=', 'ds.nis')->where('pe.tanggal', $tanggal);
            })
            ->whereIn('ds.tingkat', $tingkatAktif)
            ->selectRaw('ds.kelas,
                COUNT(DISTINCT ds.id) as total,
                COUNT(DISTINCT CASE WHEN pe.keterangan="DZUHUR" AND pe.ruang != "Izin Mens" THEN pe.nis END) as dzuhur,
                COUNT(DISTINCT CASE WHEN pe.keterangan="ASHAR" AND pe.ruang != "Izin Mens" THEN pe.nis END) as ashar,
                COUNT(DISTINCT CASE WHEN pe.ruang="Izin Mens" THEN pe.nis END) as izin')
            ->groupBy('ds.kelas')
            ->orderBy('ds.kelas')
            ->get()
            ->map(function ($row) {
                $hadir = $row->dzuhur + $row->ashar + $row->izin;
                $row->keduanya = min($row->dzuhur, $row->ashar);
                $row->alpa     = max(0, $row->total - $hadir);
                return $row;
            });

        $chartSholat = DB::table('presensiEvent')
            ->selectRaw('tanggal,
                SUM(CASE WHEN keterangan="DHUHA"  AND ruang != "Izin Mens" THEN 1 ELSE 0 END) as dhuha,
                SUM(CASE WHEN keterangan="DZUHUR" AND ruang != "Izin Mens" THEN 1 ELSE 0 END) as dzuhur,
                SUM(CASE WHEN keterangan="ASHAR"  AND ruang != "Izin Mens" THEN 1 ELSE 0 END) as ashar,
                SUM(CASE WHEN ruang="Izin Mens" THEN 1 ELSE 0 END) as izin')
            ->where('tanggal', '>=', date('Y-m-d', strtotime('-30 days', strtotime($tanggal))))
            ->where('tanggal', '<=', $tanggal)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return view('dashboard.index', compact(
            'totalHadir',
            'totalTepat',
            'totalTelat',
            'totalPulang',
            'totalDevice',
            'deviceOnline',
            'setting',
            'statusMasuk',
            'statusSholat',
            'totalDzuhur',
            'totalAshar',
            'totalIzin',
            'recentAll',
            'tanggal',
            'jam',
            'isToday',
            'chartSholat',
            'rekapKelas'
        ));
    }
}
