<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $setting = DB::table('statusnya')->first();
        return view('setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        DB::table('statusnya')->update([
            'mode'        => (int) $request->mode,
            'wa'          => $request->wa,
            'wta'         => $request->wta,
            'wtp'         => $request->wtp,
            'wtp_jumat'   => $request->wtp_jumat,
            'wp'          => $request->wp,
            'wp_jumat'    => $request->wp_jumat,
            'hari_kerja'  => (int) $request->hari_kerja,
            'auto_mode'   => (int) $request->auto_mode,
            'waktumasuk'  => $request->waktumasuk,
            'waktupulang' => $request->waktupulang,
            'info'        => $request->info,
        ]);

        return back()->with('success', 'Setting berhasil disimpan.');
    }
}