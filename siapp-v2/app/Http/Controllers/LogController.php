<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'tempreq');
        $perPage  = 50;

        // ── Tab tempreq ──
        $filterTanggal = $request->input('tanggal', date('Y-m-d'));
        $filterIp      = $request->input('ip', '');
        $filterInfo    = $request->input('info', '');
        $filterSearch  = $request->input('q', '');

        $queryTempreq = DB::table('tempreq')
            ->when($filterTanggal, fn($q) => $q->whereDate('timestamp', $filterTanggal))
            ->when($filterIp,      fn($q) => $q->where('ip', 'like', "%{$filterIp}%"))
            ->when($filterInfo,    fn($q) => $q->where('info', $filterInfo))
            ->when($filterSearch,  fn($q) => $q->where('detail', 'like', "%{$filterSearch}%"))
            ->orderBy('timestamp', 'desc');

        $tempreqLogs  = $queryTempreq->paginate($perPage)->withQueryString();
        $tempreqTotal = DB::table('tempreq')->count();
        $infoList     = DB::table('tempreq')->distinct()->orderBy('info')->pluck('info');

        // ── Tab device_logs ──
        $filterDevice  = $request->input('device', '');
        $filterTanggal2 = $request->input('tanggal2', date('Y-m-d'));

        $queryDevice = DB::table('device_logs')
            ->when($filterDevice,   fn($q) => $q->where('device_id', $filterDevice))
            ->when($filterTanggal2, fn($q) => $q->whereDate('received_at', $filterTanggal2))
            ->orderBy('received_at', 'desc');

        $deviceLogs   = $queryDevice->paginate($perPage)->withQueryString();
        $deviceTotal  = DB::table('device_logs')->count();
        $deviceList   = DB::table('device_logs')->distinct()->orderBy('device_id')->pluck('device_id');

        // ── Tab log_file ──
        $logFileDir    = '/opt/lampp/htdocs/data/uploads/';
        $filterDevice3 = $request->input('device3', '');
        $allFiles      = glob($logFileDir . '*_log_*.txt') ?: [];
        rsort($allFiles);

        $logFiles   = [];
        $deviceList3 = [];
        foreach ($allFiles as $f) {
            $base = basename($f);
            if (preg_match('/^\d{4}-\d{2}-\d{2}_log_(.+)\.txt$/', $base, $m)) {
                $deviceList3[] = $m[1];
            }
        }
        $deviceList3 = array_values(array_unique($deviceList3));
        sort($deviceList3);

        foreach ($allFiles as $f) {
            $base = basename($f);
            if (preg_match('/^(\d{4}-\d{2}-\d{2})_log_(.+)\.txt$/', $base, $m)) {
                if (!$filterDevice3 || $m[2] === $filterDevice3) {
                    $logFiles[] = [
                        'filename'  => $base,
                        'tanggal'   => $m[1],
                        'device_id' => $m[2],
                        'size'      => filesize($f),
                        'path'      => $f,
                    ];
                }
            }
        }

        return view('log.index', compact(
            'tab',
            'tempreqLogs',
            'tempreqTotal',
            'infoList',
            'filterTanggal',
            'filterIp',
            'filterInfo',
            'filterSearch',
            'deviceLogs',
            'deviceTotal',
            'deviceList',
            'filterDevice',
            'filterTanggal2',
            'logFiles',
            'deviceList3',
            'filterDevice3'
        ));
    }

    public function clearTempreq(Request $request)
    {
        $tanggal  = $request->input('tanggal');
        $all      = $request->input('all');
        $keepDays = $request->input('keep_days'); // 7 atau 30

        if ($all) {
            $deleted = DB::table('tempreq')->delete();
            return back()->with('success', "Semua request log berhasil dihapus ({$deleted} records).");
        }

        if ($request->input('before_today')) {
            $deleted = DB::table('tempreq')->whereDate('timestamp', '<', date('Y-m-d'))->delete();
            return back()->with('success', "Request log sebelum hari ini berhasil dihapus ({$deleted} records).");
        }

        if ($keepDays) {
            $batas   = now()->subDays((int)$keepDays)->toDateString();
            $deleted = DB::table('tempreq')->whereDate('timestamp', '<', $batas)->delete();
            return back()->with('success', "Request log lebih dari {$keepDays} hari berhasil dihapus ({$deleted} records).");
        }

        if ($tanggal) {
            $deleted = DB::table('tempreq')->whereDate('timestamp', $tanggal)->delete();
            return back()->with('success', "Request log tanggal {$tanggal} berhasil dihapus ({$deleted} records).");
        }

        return back()->with('error', 'Pilih opsi hapus.');
    }

    public function clearDevice(Request $request)
    {
        $device   = $request->input('device');
        $tanggal  = $request->input('tanggal');
        $all      = $request->input('all');
        $keepDays = $request->input('keep_days');

        if ($all) {
            $deleted = DB::table('device_logs')->delete();
            return back()->with('success', "Semua device log berhasil dihapus ({$deleted} records).");
        }

        if ($request->input('before_today')) {
            $deleted = DB::table('device_logs')->whereDate('received_at', '<', date('Y-m-d'))->delete();
            return back()->with('success', "Device log sebelum hari ini berhasil dihapus ({$deleted} records).");
        }

        if ($keepDays) {
            $batas   = now()->subDays((int)$keepDays)->toDateString();
            $deleted = DB::table('device_logs')->whereDate('received_at', '<', $batas)->delete();
            return back()->with('success', "Device log lebih dari {$keepDays} hari berhasil dihapus ({$deleted} records).");
        }

        $query = DB::table('device_logs');
        if ($device)  $query->where('device_id', $device);
        if ($tanggal) $query->whereDate('received_at', $tanggal);

        $deleted = $query->delete();
        return back()->with('success', "Device log berhasil dihapus ({$deleted} records).");
    }

    // ── AJAX: Data sidebar ──
    public function sidebar()
    {
        // Device list untuk MQTT log
        $mqttDevices = DB::table('device_logs')
            ->selectRaw('device_id, DATE(received_at) as tanggal, COUNT(*) as total')
            ->groupBy('device_id', 'tanggal')
            ->orderBy('device_id')
            ->orderByDesc('tanggal')
            ->get()
            ->groupBy('device_id');

        // Device list untuk File SD
        $logFileDir = '/opt/lampp/htdocs/data/uploads/';
        $allFiles   = glob($logFileDir . '*_log_*.txt') ?: [];
        rsort($allFiles);
        $sdDevices  = [];
        foreach ($allFiles as $f) {
            $base = basename($f);
            if (preg_match('/^(\d{4}-\d{2}-\d{2})_log_(.+)\.txt$/', $base, $m)) {
                $sdDevices[$m[2]][] = [
                    'tanggal'  => $m[1],
                    'filename' => $base,
                    'size'     => filesize($f),
                ];
            }
        }

        // Server log files
        $logDir     = storage_path('logs');
        $serverLogs = glob($logDir . '/laravel-*.log') ?: [];
        rsort($serverLogs);
        $serverFiles = array_map(fn($f) => [
            'filename' => basename($f),
            'size'     => filesize($f),
            'tanggal'  => preg_replace('/laravel-(.+)\.log/', '$1', basename($f)),
        ], $serverLogs);

        // Request log summary per tanggal
        $requestDates = DB::table('tempreq')
            ->selectRaw('DATE(timestamp) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return response()->json([
            'mqtt'    => $mqttDevices,
            'sd'      => $sdDevices,
            'server'  => $serverFiles,
            'request' => $requestDates,
        ]);
    }

    // ── AJAX: MQTT log per device per tanggal ──
    public function ajaxMqtt(Request $request)
    {
        $device  = $request->input('device');
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $page    = (int) $request->input('page', 1);
        $perPage = 100;

        $logs = DB::table('device_logs')
            ->when($device,  fn($q) => $q->where('device_id', $device))
            ->when($tanggal, fn($q) => $q->whereDate('received_at', $tanggal))
            ->orderBy('received_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data'      => $logs->items(),
            'total'     => $logs->total(),
            'lastPage'  => $logs->lastPage(),
            'current'   => $logs->currentPage(),
        ]);
    }

    // ── AJAX: Request log ──
    public function ajaxRequest(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $page    = (int) $request->input('page', 1);
        $perPage = 100;

        $logs = DB::table('tempreq')
            ->when($tanggal, fn($q) => $q->whereDate('timestamp', $tanggal))
            ->orderBy('timestamp', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data'     => $logs->items(),
            'total'    => $logs->total(),
            'lastPage' => $logs->lastPage(),
            'current'  => $logs->currentPage(),
        ]);
    }

    // ── AJAX: Server log content ──
    public function ajaxServer(Request $request)
    {
        $filename = $request->input('file', '');
        $logDir   = storage_path('logs');
        $path     = $logDir . '/' . basename($filename);

        if (!$filename || !file_exists($path) || !str_starts_with(realpath($path), realpath($logDir))) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        // Ambil 200 baris terakhir
        $lines = [];
        $fp    = fopen($path, 'r');
        $buffer = [];
        while (!feof($fp)) {
            $buffer[] = fgets($fp);
            if (count($buffer) > 200) array_shift($buffer);
        }
        fclose($fp);

        return response()->json([
            'filename' => basename($path),
            'size'     => filesize($path),
            'lines'    => array_values($buffer),
        ]);
    }
}
