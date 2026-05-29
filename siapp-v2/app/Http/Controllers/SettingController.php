<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $setting = DB::table('statusnya')->first();

        // Status per endpoint — ambil log terakhir per endpoint
        $endpointKeys = ['presensi_harian', 'presensi_sholat', 'izin_mens', 'izin_keluar'];
        $pushStatus = [];
        foreach ($endpointKeys as $ep) {
            $last = DB::table('push_log')
                ->where('endpoint', $ep)
                ->orderBy('created_at', 'desc')
                ->first();
            $pushStatus[$ep] = $last;
        }

        // Riwayat push — 20 terakhir
        $pushLog = DB::table('push_log')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('setting.index', compact('setting', 'pushStatus', 'pushLog'));
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
            'dhuha_start'  => $request->input('dhuha_start', '07:00:00'),
            'dhuha_end'    => $request->input('dhuha_end', '11:00:00'),
            'dzuhur_start' => $request->input('dzuhur_start', '11:30:00'),
            'dzuhur_end'   => $request->input('dzuhur_end', '13:30:00'),
            'ashar_start'  => $request->input('ashar_start', '15:00:00'),
            'ashar_end'    => $request->input('ashar_end', '16:30:00'),
            'upload1'      => $request->input('upload1', '07:30:00'),
            'upload2'      => $request->input('upload2', '13:00:00'),
            'restart1'     => $request->input('restart1', '05:00:00'),
            'restart2'     => $request->input('restart2', '17:00:00'),
            'hari_kerja'   => (int) $request->hari_kerja,
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
