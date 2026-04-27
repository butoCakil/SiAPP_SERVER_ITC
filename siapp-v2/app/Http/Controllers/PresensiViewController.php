<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiViewController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $presensi = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->orderBy('waktumasuk')
            ->get();

        $total       = $presensi->count();
        $tepat       = $presensi->where('ketmasuk', 'TW')->count();
        $telat       = $presensi->whereIn('ketmasuk', ['TL', 'TLT'])->count();
        $sudahPulang = $presensi->whereNotNull('waktupulang')
            ->filter(fn($p) => $p->waktupulang !== '00:00:00')->count();

        return view('presensi.index', compact(
            'presensi', 'tanggal', 'total', 'tepat', 'telat', 'sudahPulang'
        ));
    }

    public function event(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Ambil semua event, join ke datasiswa untuk nama + kelas
        $events = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal)
            ->select(
                'pe.nis',
                'ds.nama',
                'ds.kelas',
                'pe.keterangan',
                'pe.ruang',
                'pe.mulai',
                'pe.jam',
            )
            ->orderBy('pe.nis')
            ->orderBy('pe.jam')
            ->get();

        // Pivot: satu baris per siswa, kolom dzuhur & ashar
        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'nis'    => $nis,
                    'nama'   => $e->nama ?? '-',
                    'kelas'  => $e->kelas ?? '-',
                    'dzuhur' => null,
                    'ashar'  => null,
                    'dzuhur_izin' => false,
                    'ashar_izin'  => false,
                ];
            }
            if ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['dzuhur']      = $e->mulai;
                $siswaMap[$nis]['dzuhur_izin'] = $e->ruang === 'Izin Mens';
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['ashar']      = $e->mulai;
                $siswaMap[$nis]['ashar_izin'] = $e->ruang === 'Izin Mens';
            }
        }

        $siswaList   = collect(array_values($siswaMap));
        $totalDzuhur = $siswaList->filter(fn($s) => $s['dzuhur'])->count();
        $totalAshar  = $siswaList->filter(fn($s) => $s['ashar'])->count();
        $totalIzin   = $siswaList->filter(fn($s) => $s['dzuhur_izin'] || $s['ashar_izin'])->count();
        $totalKeduanya = $siswaList->filter(fn($s) => $s['dzuhur'] && $s['ashar'])->count();
        $totalTidakKeduanya = $siswaList->filter(fn($s) => !$s['dzuhur'] || !$s['ashar'])->count();

        return view('presensi.event', compact(
            'siswaList', 'tanggal',
            'totalDzuhur', 'totalAshar', 'totalIzin',
            'totalKeduanya', 'totalTidakKeduanya'
        ));
    }
}