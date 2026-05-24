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

    private function getData(): array
    {
        $devices      = DB::table('devices')->where('hidden', 0)->orderByRaw('online DESC, device_id ASC')->get();
        $regDevices   = DB::table('reg_device')->get()->keyBy('no_device');
        $onlineCount  = $devices->where('online', 1)->count();
        $offlineCount = $devices->where('online', 0)->count();
        return [$devices, $regDevices, $onlineCount, $offlineCount];
    }
}
