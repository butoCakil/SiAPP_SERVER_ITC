<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimController extends Controller
{
    public function presensi(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $data = DB::table('datapresensi')
            ->where('tanggal', $tanggal)
            ->orderBy('waktumasuk')
            ->get();
        return response()->json([
            'status'  => 'ok',
            'tanggal' => $tanggal,
            'total'   => $data->count(),
            'data'    => $data,
        ]);
    }

    public function presensiRange(Request $request)
    {
        $dari   = $request->input('dari', date('Y-m-d'));
        $sampai = $request->input('sampai', date('Y-m-d'));
        $data = DB::table('datapresensi')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->orderBy('waktumasuk')
            ->get();
        return response()->json([
            'status'  => 'ok',
            'dari'    => $dari,
            'sampai'  => $sampai,
            'total'   => $data->count(),
            'data'    => $data,
        ]);
    }

    public function siswa(Request $request)
    {
        $data = DB::table('datasiswa')
            ->select(
                'nis', 'nama', 'nick', 'kelas',
                'tingkat', 'jur', 'kelompok',
                'foto', 'keterangan'
            )
            ->get();
        return response()->json([
            'status' => 'ok',
            'total'  => $data->count(),
            'data'   => $data,
        ]);
    }
}
