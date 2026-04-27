<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tanggal = date('Y-m-d');
        $jam     = date('H:i:s');

        // ── Presensi harian ──
        $totalHadir  = DB::table('datapresensi')->where('tanggal', $tanggal)->count();
        $totalTepat  = DB::table('datapresensi')->where('tanggal', $tanggal)->where('ketmasuk', 'TW')->count();
        $totalTelat  = DB::table('datapresensi')->where('tanggal', $tanggal)->whereIn('ketmasuk', ['TL', 'TLT'])->count();
        $totalPulang = DB::table('datapresensi')->where('tanggal', $tanggal)->whereNotNull('waktupulang')->where('waktupulang', '!=', '00:00:00')->count();

        // ── Device ──
        $totalDevice  = DB::table('devices')->count();
        $deviceOnline = DB::table('devices')->where('online', 1)->count();

        // ── Setting ──
        $setting = DB::table('statusnya')->first();

        $statusMasuk = match((int)($setting->mode ?? 0)) {
            1 => ['label' => 'MASUK AKTIF',  'color' => 'success',   'icon' => 'door-open'],
            2 => ['label' => 'PULANG AKTIF', 'color' => 'warning',   'icon' => 'door-closed'],
            default => ['label' => 'DITUTUP','color' => 'secondary', 'icon' => 'ban'],
        };

        // ── Status sholat ──
        $batasDzuhurMulai   = '11:45:00';
        $batasDzuhurSelesai = '14:30:00';
        $batasAsharMulai    = '14:30:01';
        $batasAsharSelesai  = '17:00:00';

        if ($jam >= $batasDzuhurMulai && $jam <= $batasDzuhurSelesai) {
            $statusSholat = ['label' => 'DZUHUR AKTIF',  'color' => 'warning',   'bg' => '#ff8800'];
        } elseif ($jam >= $batasAsharMulai && $jam <= $batasAsharSelesai) {
            $statusSholat = ['label' => 'ASHAR AKTIF',   'color' => 'secondary', 'bg' => '#9c27b0'];
        } else {
            $statusSholat = ['label' => 'DILUAR WAKTU',  'color' => 'secondary', 'bg' => '#9e9e9e'];
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
            ->select('ds.nama', 'ds.kelas as info', 'pe.jam as waktu',
                     'pe.keterangan as ket', 'pe.ruang as device', 'pe.timestamp as time')
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

        return view('dashboard.index', compact(
            'totalHadir', 'totalTepat', 'totalTelat', 'totalPulang',
            'totalDevice', 'deviceOnline',
            'setting', 'statusMasuk', 'statusSholat',
            'totalDzuhur', 'totalAshar', 'totalIzin',
            'recentAll', 'tanggal', 'jam'
        ));
    }
}