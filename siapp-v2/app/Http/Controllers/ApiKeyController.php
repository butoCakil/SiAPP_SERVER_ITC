<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = DB::table('api')->orderBy('jenis')->orderBy('id')->get();
        return view('apikey.index', compact('keys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'info_api'    => 'required',
            'jenis'       => 'required|in:device_token,sim_token',
            'masaberlaku' => 'required|date',
            'status'      => 'required',
        ]);

        $kode = $request->input('kode_api')
            ? $request->input('kode_api')
            : md5(Str::random(32) . time());

        DB::table('api')->insert([
            'kode_api'    => $kode,
            'info_api'    => $request->info_api,
            'jenis'       => $request->jenis,
            'masaberlaku' => $request->masaberlaku,
            'status'      => $request->status,
        ]);

        return redirect()->route('apikey')->with('success', 'API Key berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $data = [
            'info_api'    => $request->info_api,
            'jenis'       => $request->jenis,
            'masaberlaku' => $request->masaberlaku,
            'status'      => $request->status,
        ];

        if ($request->input('regenerate')) {
            $data['kode_api'] = md5(Str::random(32) . time());
        } elseif ($request->input('kode_api')) {
            $data['kode_api'] = $request->input('kode_api');
        }

        DB::table('api')->where('id', $id)->update($data);
        return redirect()->route('apikey')->with('success', 'API Key berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        DB::table('api')->where('id', $id)->delete();
        return redirect()->route('apikey')->with('success', 'API Key berhasil dihapus.');
    }
}