<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaViewController extends Controller
{
    public function index(Request $request)
    {
        $siswa     = DB::table('datasiswa')->orderBy('kelas')->orderBy('nama')->get();
        $kelasList = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');
        return view('siswa.index', compact('siswa', 'kelasList'));
    }

    public function create()
    {
        return view('siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'    => 'required|unique:datasiswa,nis',
            'nama'   => 'required',
            'kelas'  => 'required',
            'tingkat'=> 'required',
        ]);

        DB::table('datasiswa')->insert([
            'nokartu'    => strtoupper($request->nokartu ?? ''),
            'nis'        => $request->nis,
            'nama'       => strtoupper($request->nama),
            'nick'       => $request->nick ?? strtolower($request->nis),
            'kelas'      => $request->kelas,
            'tingkat'    => $request->tingkat,
            'jur'        => $request->jur ?? '',
            'kode'       => $request->kode ?? '',
            'foto'       => 'default.jpg',
            'kelompok'   => $request->kelompok ?? '1',
            'keterangan' => $request->keterangan ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('siswa')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function editSiswa(int $id)
    {
        $siswa = DB::table('datasiswa')->where('id', $id)->first();
        if (!$siswa) abort(404);
        return view('siswa.edit', compact('siswa'));
    }

    public function updateSiswa(Request $request, int $id)
    {
        DB::table('datasiswa')->where('id', $id)->update([
            'nokartu'    => strtoupper($request->nokartu ?? ''),
            'nama'       => strtoupper($request->nama),
            'kelas'      => $request->kelas,
            'tingkat'    => $request->tingkat,
            'jur'        => $request->jur ?? '',
            'kode'       => $request->kode ?? '',
            'keterangan' => $request->keterangan ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('siswa')->with('success', 'Data siswa berhasil diupdate.');
    }

    public function destroySiswa(int $id)
    {
        DB::table('datasiswa')->where('id', $id)->delete();
        return redirect()->route('siswa')->with('success', 'Siswa berhasil dihapus.');
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