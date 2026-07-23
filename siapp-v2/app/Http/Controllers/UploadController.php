<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadController extends Controller
{
    // ── Helper: baca JSON dari multipart file atau raw body ──
    private function readJson(Request $request): ?array
    {
        if ($request->hasFile('file')) {
            $json = file_get_contents($request->file('file')->getRealPath());
        } else {
            $json = $request->getContent();
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) return null;
        return $data;
    }

    // ── Helper: selisih waktu ──
    private function selisihWaktu(string $waktu1, string $waktu2): string
    {
        $waktu1 = trim(str_replace(' ', '', $waktu1));
        $waktu2 = trim(str_replace(' ', '', $waktu2));
        if ($waktu1 === '' || $waktu1 === '0') $waktu1 = '00:00:00';
        if ($waktu2 === '' || $waktu2 === '0') $waktu2 = '00:00:00';
        try {
            $t1 = new \DateTime($waktu1);
            $t2 = new \DateTime($waktu2);
        } catch (\Exception $e) {
            $t1 = new \DateTime('00:00:00');
            $t2 = new \DateTime('00:00:00');
        }
        return $t1->diff($t2)->format('%H:%I:%S');
    }

    // ── Helper: simpan JSON backup ──
    private function saveBackup(string $folder, string $filename, array $content): void
    {
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        file_put_contents(
            "$folder/$filename",
            json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    // ════════════════════════════════════════════
    // POST /api/upload/presensi
    // ════════════════════════════════════════════
    public function presensi(Request $request)
    {
        $data = $this->readJson($request);
        if (!$data || !isset($data['data']) || !is_array($data['data'])) {
            return response()->json(['status' => 'error', 'message' => 'Data JSON tidak valid']);
        }

        $meta_nodevice   = $data['nodevice']   ?? '0';
        $meta_chipID     = $data['chipID']      ?? '';
        $meta_macAddress = $data['macAddress']  ?? request()->ip();
        $meta_ipAddress  = $data['ipAddress']   ?? request()->ip();
        $meta_timestamp  = $data['timestamp']   ?? now()->format('Y-m-d H:i:s');

        // === BARU: normalisasi array->string, dipertahankan dari kode asli ===
        $nodevice_bind = is_array($meta_nodevice)   ? ($meta_nodevice[0]   ?? '0') : $meta_nodevice;
        $mac_bind      = is_array($meta_macAddress) ? ($meta_macAddress[0] ?? '0') : $meta_macAddress;

        // JSON backup
        $folder    = '/opt/lampp/htdocs/data/jsonPresensi';
        $today     = now()->format('Y-m-d');
        $json_file = "$folder/presensi-$today.json";

        $json_content = [];
        if (file_exists($json_file)) {
            $json_content = json_decode(file_get_contents($json_file), true) ?? [];
        }
        if (!isset($json_content['data']))   $json_content['data']   = [];
        if (!isset($json_content['recent'])) $json_content['recent'] = [];

        // === BARU: logic dedup+insert/update dipindah ke BatchUploadService ===
        $result = app(\App\Services\BatchUploadService::class)
            ->prosesPresensi($data['data'], $nodevice_bind, $mac_bind, $meta_timestamp);
        $inserted = $result['inserted'];
        $updated  = $result['updated'];
        $skipped  = $result['skipped'];
        $errors   = $result['errors'];

        // Update JSON backup (khusus jalur HTTP) -- pakai entries dari service
        foreach ($result['entries'] as $json_entry) {
            $found = false;
            foreach ($json_content['data'] as &$ex) {
                if (($ex['nokartu'] ?? '') === $json_entry['nokartu'] && ($ex['tanggal'] ?? '') === $json_entry['tanggal']) {
                    $ex = $json_entry;
                    $found = true;
                    break;
                }
            }
            unset($ex);
            if (!$found) $json_content['data'][] = $json_entry;
        }

        // Update recent di JSON backup (tidak berubah)
        $found = false;
        foreach ($json_content['recent'] as &$entry) {
            if (($entry['chipID'] ?? '') === $meta_chipID) {
                if (end($entry['macAddress']) !== $meta_macAddress) $entry['macAddress'][] = $meta_macAddress;
                if (end($entry['nodevice'])   !== $meta_nodevice)   $entry['nodevice'][]   = $meta_nodevice;
                if (end($entry['ipAddress'])  !== $meta_ipAddress)  $entry['ipAddress'][]  = $meta_ipAddress;
                if (end($entry['timestamp'])  !== $meta_timestamp)  $entry['timestamp'][]  = $meta_timestamp;
                $entry['last_update'] = now()->format('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        unset($entry);
        if (!$found) {
            $json_content['recent'][] = [
                'chipID'      => $meta_chipID,
                'macAddress'  => [$meta_macAddress],
                'nodevice'    => [$meta_nodevice],
                'ipAddress'   => [$meta_ipAddress],
                'timestamp'   => [$meta_timestamp],
                'last_update' => now()->format('Y-m-d H:i:s'),
            ];
        }

        $this->saveBackup($folder, "presensi-$today.json", $json_content);

        return response()->json([
            'status'    => ($inserted + $updated) > 0 ? 'Success' : 'noChanges',
            'inserted'  => $inserted,
            'updated'   => $updated,
            'skipped'   => $skipped,
            'errors'    => $errors,
            'json_file' => "presensi-$today.json",
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    // ════════════════════════════════════════════
    // POST /api/upload/sholat
    // ════════════════════════════════════════════
    public function sholat(Request $request)
    {
        $data = $this->readJson($request);
        if (!$data || !isset($data['data']) || !is_array($data['data'])) {
            return response()->json(['status' => 'error', 'message' => 'Data JSON tidak valid']);
        }

        $meta_nodevice = $data['nodevice'] ?? '0';
        $folder        = '/opt/lampp/htdocs/data/jsonSholat';
        $today         = now()->format('Y-m-d');
        $json_file     = "$folder/sholat-$today.json";

        $json_content = [];
        if (file_exists($json_file)) {
            $json_content = json_decode(file_get_contents($json_file), true) ?? [];
        }
        if (!isset($json_content['data'])) $json_content['data'] = [];

        // === BARU: logic dedup+insert dipindah ke BatchUploadService, dipakai bersama MQTT ===
        $result = app(\App\Services\BatchUploadService::class)->prosesSholat($data['data'], $meta_nodevice);
        $inserted = $result['inserted'];
        $skipped  = $result['skipped'];
        $errors   = $result['errors'];

        // Backup JSON tetap di sini (khusus jalur HTTP) -- pakai insertedEntries dari service
        foreach ($result['insertedEntries'] as $json_entry) {
            $found = false;
            foreach ($json_content['data'] as &$ex) {
                if (($ex['nokartu'] ?? '') === $json_entry['nokartu']
                    && ($ex['keterangan'] ?? '') === $json_entry['keterangan']
                    && ($ex['tanggal'] ?? '') === $json_entry['tanggal']
                ) {
                    $ex = $json_entry;
                    $found = true;
                    break;
                }
            }
            unset($ex);
            if (!$found) $json_content['data'][] = $json_entry;
        }

        $this->saveBackup($folder, "sholat-$today.json", $json_content);

        return response()->json([
            'status'    => $inserted > 0 ? 'success' : 'noChanges',
            'inserted'  => $inserted,
            'skipped'   => $skipped,
            'errors'    => $errors,
            'json_file' => "sholat-$today.json",
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    // ════════════════════════════════════════════
    // POST /api/upload/izinsholat
    // ════════════════════════════════════════════
    public function izinSholat(Request $request)
    {
        $data = $this->readJson($request);
        if (!$data || !isset($data['data']) || !is_array($data['data'])) {
            return response()->json(['status' => 'error', 'message' => 'Data JSON tidak valid']);
        }

        $meta_nodevice = $data['nodevice'] ?? '0';
        $folder        = '/opt/lampp/htdocs/data/jsonIzinSholat';
        $today         = now()->format('Y-m-d');
        $json_file     = "$folder/izinmens-$today.json";

        $json_content = [];
        if (file_exists($json_file)) {
            $json_content = json_decode(file_get_contents($json_file), true) ?? [];
        }
        if (!isset($json_content['data'])) $json_content['data'] = [];

        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $RUANG = 'Izin Mens';

        foreach ($data['data'] as $item) {
            if (!is_array($item)) continue;

            $nokartu = trim($item['i'] ?? '');
            $nis     = trim($item['n'] ?? '');
            $sesi    = strtoupper(trim($item['s'] ?? ''));
            $waktu   = trim($item['t'] ?? '');

            if (!$nokartu || !$nis || !$sesi || !$waktu) {
                $errors[] = "Data tidak lengkap untuk NIS: $nis";
                continue;
            }

            $tanggal = substr($waktu, 0, 10);
            $jam     = substr($waktu, 11, 8);

            $map = [];
            if (str_contains($sesi, 'H')) $map[] = ['DHUHA',  $tanggal, $jam];
            if (str_contains($sesi, 'D')) $map[] = ['DZUHUR', $tanggal, $jam];
            if (str_contains($sesi, 'A')) $map[] = ['ASHAR',  $tanggal, $jam];

            if (empty($map)) {
                $errors[] = "Sesi tidak dikenal ($sesi) NIS: $nis";
                continue;
            }

            foreach ($map as [$ket, $tgl, $j]) {
                $exist = DB::table('presensiEvent')
                    ->where('nokartu', $nokartu)
                    ->where('tanggal', $tgl)
                    ->where('keterangan', $ket)
                    ->where('ruang', 'Izin Mens')
                    ->exists();

                if ($exist) {
                    $skipped++;
                    continue;
                }

                DB::table('presensiEvent')->insert([
                    'nokartu'    => $nokartu,
                    'nis'        => $nis,
                    'ruang'      => $RUANG,
                    'mulai'      => $j,
                    'selesai'    => null,
                    'jam'        => $j,
                    'tanggal'    => $tgl,
                    'keterangan' => $ket,
                ]);
                $inserted++;

                $json_entry = ['nokartu' => $nokartu, 'nis' => $nis, 'tanggal' => $tgl, 'jam' => $j, 'keterangan' => $ket, 'nodevice' => $meta_nodevice];
                $found = false;
                foreach ($json_content['data'] as &$ex) {
                    if (($ex['nokartu'] ?? '') === $nokartu && ($ex['keterangan'] ?? '') === $ket && ($ex['tanggal'] ?? '') === $tgl) {
                        $ex = $json_entry;
                        $found = true;
                        break;
                    }
                }
                unset($ex);
                if (!$found) $json_content['data'][] = $json_entry;
            }
        }

        $this->saveBackup($folder, "izinmens-$today.json", $json_content);

        return response()->json([
            'status'    => $inserted > 0 ? 'success' : 'noChanges',
            'inserted'  => $inserted,
            'skipped'   => $skipped,
            'errors'    => $errors,
            'json_file' => "izinmens-$today.json",
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    // ════════════════════════════════════════════
    // POST /api/upload/file
    // ════════════════════════════════════════════
    public function file(Request $request)
    {
        $today  = now()->format('Y-m-d');
        $folder = '/opt/lampp/htdocs/data/uploads';
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $originalName = $request->file('file')->getClientOriginalName();
            // Hindari double prefix tanggal
            $fileName = preg_match('/^\d{4}-\d{2}-\d{2}_/', $originalName)
                ? basename($originalName)
                : $today . '_' . basename($originalName);
            $request->file('file')->move($folder, $fileName);
            return response("File berhasil diunggah: $fileName", 200);
        }

        return response("Tidak ada file yang diterima.", 400);
    }
}
