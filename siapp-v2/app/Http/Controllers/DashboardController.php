<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tanggal = date('Y-m-d');

        $totalHadir = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->whereNotNull('waktumasuk')
            ->count();

        $totalTepat = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->where('ketmasuk', 'TW')
            ->count();

        $totalTelat = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->whereIn('ketmasuk', ['TL', 'TLT'])
            ->count();

        $totalPulang = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->whereNotNull('waktupulang')
            ->count();

        $totalDevice = DB::table('devices')->count();
        $deviceOnline = DB::table('devices')->where('online', 1)->count();

        $setting = DB::table('statusnya')->first();

        $presensiTerbaru = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalHadir', 'totalTepat', 'totalTelat',
            'totalPulang', 'totalDevice', 'deviceOnline',
            'setting', 'presensiTerbaru', 'tanggal'
        ));
    }
}