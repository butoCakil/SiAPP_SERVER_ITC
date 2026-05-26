<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DeviceViewController extends Controller
{
    public function index()
    {
        [$devices, $regDevices, $onlineCount, $offlineCount] = $this->getData();
        return view('device.index', compact('devices', 'regDevices', 'onlineCount', 'offlineCount'));
    }

    public function cards()
    {
        [$devices, $regDevices] = $this->getData();
        return view('device._cards', compact('devices', 'regDevices'));
    }

    public function destroy(Request $request, string $id)
    {
        DB::table('devices')->where('device_id', $id)->update(['hidden' => 1]);
        return response()->json(['status' => 'ok']);
    }

    // ── Registrasi Device ──
    public function registrasi()
    {
        $regDevices = DB::table('reg_device')->orderBy('kode')->orderBy('no_device')->get();
        $kodeList   = ['GATE', 'MASJID', 'EVENT', 'GATETL'];
        return view('device.registrasi', compact('regDevices', 'kodeList'));
    }

    public function storeReg(Request $request)
    {
        $request->validate([
            'chip_id'    => 'required',
            'no_device'  => 'required|unique:reg_device,no_device',
            'kode'       => 'required',
            'info_device' => 'required',
            'status'     => 'required',
        ]);

        DB::table('reg_device')->insert([
            'chip_id'    => trim($request->chip_id),
            'no_device'  => strtoupper(trim($request->no_device)),
            'kode'       => strtoupper(trim($request->kode)),
            'info_device' => $request->info_device,
            'status'     => $request->status,
        ]);

        return redirect()->route('device.registrasi')
            ->with('success', 'Device berhasil didaftarkan.');
    }

    public function updateReg(Request $request, int $id)
    {
        DB::table('reg_device')->where('id', $id)->update([
            'chip_id'    => trim($request->chip_id),
            'kode'       => strtoupper(trim($request->kode)),
            'info_device' => $request->info_device,
            'status'     => $request->status,
        ]);

        return redirect()->route('device.registrasi')
            ->with('success', 'Data device berhasil diupdate.');
    }

    public function destroyReg(int $id)
    {
        DB::table('reg_device')->where('id', $id)->delete();
        return redirect()->route('device.registrasi')
            ->with('success', 'Device berhasil dihapus dari registrasi.');
    }

    public function detail(string $id)
    {
        $device  = DB::table('devices')->where('device_id', $id)->first();
        if (!$device) abort(404);

        $reg     = DB::table('reg_device')->where('no_device', $id)->first();
        $metrics = DB::table('device_metrics')
            ->where('device_id', $id)
            ->orderBy('recorded_at', 'asc')
            ->get(['ram', 'rssi', 'ping', 'buffer', 'recorded_at']);

        $status   = json_decode($device->last_status,  true) ?? [];
        $setting  = json_decode($device->last_setting, true) ?? [];
        $command  = json_decode($device->last_command, true) ?? [];

        // Daftar tanggal log
        $uploadDir = '/opt/lampp/htdocs/data/uploads/';
        $files     = glob($uploadDir . '*_log_' . $id . '.txt');
        $logDates  = [];
        foreach ($files as $f) {
            if (preg_match('/^(\d{4}-\d{2}-\d{2})_log_/', basename($f), $m)) {
                $logDates[] = $m[1];
            }
        }
        rsort($logDates);

        return view('device.detail', compact(
            'device', 'reg', 'metrics', 'status', 'setting', 'command', 'logDates', 'id'
        ));
    }

    public function logViewer(Request $request, string $id)
    {
        $tanggal   = $request->input('tanggal', date('Y-m-d'));
        $uploadDir = '/opt/lampp/htdocs/data/uploads/';

        // Ambil daftar tanggal yang ada untuk device ini
        $files = glob($uploadDir . '*_log_' . $id . '.txt');
        $tanggalList = [];
        foreach ($files as $f) {
            $base = basename($f);
            if (preg_match('/^(\d{4}-\d{2}-\d{2})_log_/', $base, $m)) {
                $tanggalList[] = $m[1];
            }
        }
        rsort($tanggalList);

        // Baca isi log
        $logFile  = $uploadDir . $tanggal . '_log_' . $id . '.txt';
        $logLines = [];
        if (file_exists($logFile)) {
            $logLines = array_filter(
                explode("\n", file_get_contents($logFile)),
                fn($l) => trim($l) !== ''
            );
            $logLines = array_values($logLines);
        }

        // Info device
        $device = DB::table('devices')->where('device_id', $id)->first();

        return view('device.log', compact(
            'id',
            'tanggal',
            'tanggalList',
            'logLines',
            'device'
        ));
    }

    public function kirimKoneksi(Request $request, string $id)
    {
        $koneksi = [];

        if ($request->filled('wifi_index'))   $koneksi['wifi_index']   = (int) $request->wifi_index;
        if ($request->filled('upload_index')) $koneksi['upload_index'] = (int) $request->upload_index;
        if ($request->filled('db_index'))     $koneksi['db_index']     = (int) $request->db_index;
        if ($request->filled('mode_device')) $koneksi['mode_device'] = (int) $request->mode_device;

        if (empty($koneksi)) {
            return redirect()->route('device.detail', $id)->with('error', 'Tidak ada perubahan.');
        }

        $service = new \App\Services\DeviceService();
        $result  = $service->kirimKoneksi($id, $koneksi);

        // Simpan last_koneksi ke DB
        $wifiPresets = [
            0=>'Instruktur-TE', 1=>'Instruktur-MM', 2=>'WIFI-RFID-13',
            3=>'WIFI-RFID-14', 4=>'WIFI-RFID-152', 5=>'HOTSPOT-SKANEBA',
            6=>'HOTSPOT-SISWA', 7=>'HOTSPOT-SKANEBA-ITC', 8=>'mqtt', 9=>'bumblebee',
        ];
        $uploadPresets = [
            0=>'upload Presensi', 1=>'upload Sholat',
            2=>'upload Izin', 3=>'upload Izin Mens',
        ];

        $dbPresets = [
            0 => 'fakeRestApi', 1 => 'fakeRestApiMid',
            2 => 'restAPI/datasiswa', 3 => 'restAPI/datagtk', 4 => 'restAPI/data',
        ];

        $modePresets = [
            0 => 'Normal', 
            1 => 'Sholat', 
            2 => 'Full Online'
        ];
        
        // Ambil data lama, merge dengan yang baru
        $existing = DB::table('devices')->where('device_id', $id)->value('last_koneksi');
        $koneksiLama = $existing ? json_decode($existing, true) : [];

        $koneksiInfo = array_merge($koneksiLama, array_filter([
            'wifi_index'    => $request->filled('wifi_index') ? (int)$request->wifi_index : null,
            'wifi_nama'     => $request->filled('wifi_index') ? ($wifiPresets[(int)$request->wifi_index] ?? '-') : null,
            'upload_index'  => $request->filled('upload_index') ? (int)$request->upload_index : null,
            'upload_nama'   => $request->filled('upload_index') ? ($uploadPresets[(int)$request->upload_index] ?? '-') : null,
            'db_index'    => $request->filled('db_index') ? (int)$request->db_index : null,
            'db_nama'     => $request->filled('db_index') ? ($dbPresets[(int)$request->db_index] ?? '-') : null,
            'mode_device' => $request->filled('mode_device') ? (int)$request->mode_device : null,
            'mode_nama'   => $request->filled('mode_device') ? ($modePresets[(int)$request->mode_device] ?? '-') : null,
        ], fn($v) => $v !== null));

        $koneksiInfo['timestamp'] = now()->format('Y-m-d H:i:s');
        DB::table('devices')->where('device_id', $id)->update([
            'last_koneksi' => json_encode($koneksiInfo, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('device.detail', $id)
            ->with('success', 'Koneksi berhasil dikirim ke device.');
    }

    private function getData(): array
    {
        $devices      = DB::table('devices')->where('hidden', 0)->orderByRaw('online DESC, device_id ASC')->get();
        $regDevices   = DB::table('reg_device')->get()->keyBy('no_device');
        $onlineCount  = $devices->where('online', 1)->count();
        $offlineCount = $devices->where('online', 0)->count();
        return [$devices, $regDevices, $onlineCount, $offlineCount];
    }

    public function listDir(Request $request, string $id)
    {
        $path = $request->input('path', '/');
        $service = new \App\Services\DeviceService();
        $service->kirimCommand($id, ['listDir' => $path]);
        return response()->json(['status' => 'ok', 'message' => 'Perintah listDir terkirim']);
    }

    public function getDirList(string $id)
    {
        $device = DB::table('devices')->where('device_id', $id)->first();
        if (!$device || !$device->last_dirlist) {
            return response()->json(['status' => 'empty', 'data' => null]);
        }
        return response()->json(['status' => 'ok', 'data' => json_decode($device->last_dirlist, true)]);
    }

    public function uploadFileSd(Request $request, string $id)
    {
        $path = $request->input('path');
        $url  = $request->input('url');
        if (!$path || !$url) {
            return response()->json(['status' => 'error', 'message' => 'path dan url wajib diisi']);
        }
        $service = new \App\Services\DeviceService();
        $service->kirimCommand($id, ['uploadFile' => $path, 'uploadUrl' => $url]);
        return response()->json(['status' => 'ok', 'message' => 'Perintah uploadFile terkirim']);
    }

    public function updateLabel(Request $request, string $id)
    {
        $label = $request->input('label', '');

        $exists = DB::table('reg_device')->where('no_device', $id)->exists();
        if ($exists) {
            DB::table('reg_device')->where('no_device', $id)->update([
                'info_device' => $label,
            ]);
        } else {
            // Simpan ke kolom info di tabel devices sebagai fallback
            DB::table('devices')->where('device_id', $id)->update([
                'info' => json_encode(['label' => $label], JSON_UNESCAPED_UNICODE),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
