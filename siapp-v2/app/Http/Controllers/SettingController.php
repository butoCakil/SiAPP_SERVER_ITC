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
        $tingkatAktif = json_encode($request->input('tingkat_aktif', ['X', 'XI', 'XII']));

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
            'tingkat_aktif' => $tingkatAktif,
            'log_retention'      => (int) $request->input('log_retention', 30),
            'timid_presensi_url' => $request->input('timid_presensi_url', ''),
            'timid_sholat_url'   => $request->input('timid_sholat_url', ''),
            'timid_izin_mens_url' => $request->input('timid_izin_mens_url', ''),
            'timid_ijin_url'     => $request->input('timid_ijin_url', ''),
            'timid_api_key'      => $request->input('timid_api_key', ''),
            'push_interval'      => (int) $request->input('push_interval', 5),
            'wa_number'          => $request->input('wa_number', ''),
            'wa_numbers'         => json_encode(array_filter($request->input('wa_numbers', []))),
            'wa_device_id'       => $request->input('wa_device_id', ''),
            'offline_after'      => (int) $request->input('offline_after', 120),
            'escalation_after'   => (int) $request->input('escalation_after', 300),
            'notif_quiet_start'  => (int) $request->input('notif_quiet_start', 18),
            'notif_quiet_end'    => (int) $request->input('notif_quiet_end', 6),
            'notif_escalation_start' => (int) $request->input('notif_escalation_start', 10),
            'notif_escalation_end'   => (int) $request->input('notif_escalation_end', 16),
        ]);

        return back()->with('success', 'Setting berhasil disimpan.');
    }
}
