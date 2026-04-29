<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tanggal     = date('Y-m-d');
        $jam         = date('H:i:s');
        $setting     = DB::table('statusnya')->first();
        $filterKelas = $request->input('kelas', '');
        $tab         = $request->input('tab', 'presensi');

        // ── Status presensi masuk/pulang ──
        $statusMasuk = match((int)($setting->mode ?? 0)) {
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
            ->orderBy('pe.timestamp', 'desc')
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
                    'last_time'   => $e->timestamp,
                ];
            }
            if ($e->keterangan === 'DZUHUR') $sholatMap[$nis]['dzuhur'] = $e->mulai;
            if ($e->keterangan === 'ASHAR')  $sholatMap[$nis]['ashar']  = $e->mulai;
            if ($e->ruang === 'Izin Mens')   $sholatMap[$nis]['izin_mens'] = true;
        }
        // $sholatList = collect(array_values($sholatMap))->sortByDesc('last_time')->take(30)->values();
        $sholatList = collect(array_values($sholatMap))->sortByDesc('last_time')->values();

        return view('home', compact(
            'setting', 'statusMasuk', 'statusSholat',
            'totalHadir', 'totalDzuhur', 'totalAshar', 'totalIzin',
            'kelasList', 'filterKelas', 'tab',
            'recentPresensi', 'sholatList', 'tanggal', 'jam'
        ));
    }
}