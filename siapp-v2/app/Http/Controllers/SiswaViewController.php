<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaViewController extends Controller
{
    public function index(Request $request)
    {
        // Load semua siswa sekaligus — search via JS
        $siswa     = DB::table('datasiswa')->orderBy('kelas')->orderBy('nama')->get();
        $kelasList = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('siswa.index', compact('siswa', 'kelasList'));
    }

    public function updateKartu(Request $request)
    {
        $nokartu = strtoupper(trim($request->input('nokartu', '')));
        $id      = $request->input('id');
        $db      = $request->input('db', 'datasiswa');

        if (!$nokartu || !$id) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap'], 400);
        }

        $adaDiGuru = DB::table('dataguru')->where('nokartu', $nokartu)->exists();
        if ($adaDiGuru) {
            return response()->json(['status' => 'error', 'message' => 'Kartu sudah digunakan oleh GTK']);
        }

        $adaDiSiswa = DB::table('datasiswa')->where('nokartu', $nokartu)->where('id', '!=', $id)->exists();
        if ($adaDiSiswa) {
            return response()->json(['status' => 'error', 'message' => 'Kartu sudah digunakan oleh siswa lain']);
        }

        $table = $db === 'dataguru' ? 'dataguru' : 'datasiswa';
        DB::table($table)->where('id', $id)->update(['nokartu' => $nokartu]);
        DB::table('tmprfid')->delete();

        return response()->json(['status' => 'ok', 'message' => 'Kartu berhasil diupdate']);
    }

    public function tmprfid()
    {
        $nokartu = DB::table('tmprfid')->value('nokartu_admin');
        return response()->json(['nokartu' => $nokartu ?? '']);
    }

    public function tagKartu(Request $request)
    {
        $nokartu = strtoupper(trim($request->input('nokartu', '')));

        if (!$nokartu || strlen($nokartu) !== 8) {
            return response('Value tidak diijinkan', 400);
        }

        if (!preg_match('/^[A-F0-9]{8}$/', $nokartu)) {
            return response('Input tidak valid', 400);
        }

        DB::table('tmprfid')->delete();
        DB::table('tmprfid')->insert([
            'nokartu'       => $nokartu,
            'nokartu_admin' => $nokartu,
        ]);

        return response('Berhasil');
    }
}