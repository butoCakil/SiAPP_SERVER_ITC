<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiViewController extends Controller
{
    public function index(Request $request)
    {
        $tanggal  = $request->input('tanggal', date('Y-m-d'));
        $kelas    = $request->input('kelas', '');
        $filterKet = $request->input('ket', '');

        $query = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->orderBy('waktumasuk');

        if ($kelas) {
            $query->where('info', $kelas);
        }

        if ($filterKet === 'terlambat') {
            $query->whereIn('ketmasuk', ['TL', 'TLT', 'T']);
        } elseif ($filterKet === 'tepat') {
            $query->where('ketmasuk', 'TW');
        } elseif ($filterKet === 'pulang_awal') {
            $query->where('ketpulang', 'PA');
        }

        $presensi = $query->get();

        $total       = $presensi->count();
        $tepat       = $presensi->where('ketmasuk', 'TW')->count();
        $telat       = $presensi->whereIn('ketmasuk', ['TL', 'TLT', 'T'])->count();
        $sudahPulang = $presensi->filter(fn($p) => $p->waktupulang && $p->waktupulang !== '00:00:00')->count();

        $kelasList = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->whereNotNull('info')
            ->distinct()
            ->orderBy('info')
            ->pluck('info');

        return view('presensi.index', compact(
            'presensi', 'tanggal', 'total', 'tepat', 'telat', 'sudahPulang',
            'kelasList', 'kelas', 'filterKet'
        ));
    }

    public function event(Request $request)
    {
        $tanggal    = $request->input('tanggal', date('Y-m-d'));
        $filterKelas = $request->input('kelas', '');

        $query = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal);

        if ($filterKelas) {
            $query->where('ds.kelas', $filterKelas);
        }

        $events = $query
            ->select(
                'pe.nis', 'ds.nama', 'ds.kelas',
                'pe.keterangan', 'pe.ruang', 'pe.mulai', 'pe.jam',
            )
            ->orderBy('pe.nis')
            ->orderBy('pe.jam')
            ->get();

        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'nis'         => $nis,
                    'nama'        => $e->nama ?? '-',
                    'kelas'       => $e->kelas ?? '-',
                    'dzuhur'      => null,
                    'ashar'       => null,
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

        $siswaList      = collect(array_values($siswaMap));
        $totalDzuhur    = $siswaList->filter(fn($s) => $s['dzuhur'])->count();
        $totalAshar     = $siswaList->filter(fn($s) => $s['ashar'])->count();
        $totalIzin      = $siswaList->filter(fn($s) => $s['dzuhur_izin'] || $s['ashar_izin'])->count();
        $totalKeduanya  = $siswaList->filter(fn($s) => $s['dzuhur'] && $s['ashar'])->count();
        $totalTidakKeduanya = $siswaList->filter(fn($s) => !$s['dzuhur'] || !$s['ashar'])->count();

        $kelasList = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('presensi.event', compact(
            'siswaList', 'tanggal', 'filterKelas', 'kelasList',
            'totalDzuhur', 'totalAshar', 'totalIzin',
            'totalKeduanya', 'totalTidakKeduanya'
        ));
    }
}