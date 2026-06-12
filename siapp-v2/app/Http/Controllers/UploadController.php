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

        // Ambil waktu masuk & pulang dari statusnya
        $setting     = DB::table('statusnya')->first();
        $waktumasuk  = $setting->waktumasuk  ?? '00:00:00';
        $waktupulang = $setting->waktupulang ?? '00:00:00';

        // JSON backup
        $folder    = '/opt/lampp/htdocs/data/jsonPresensi';
        $today     = now()->format('Y-m-d');
        $json_file = "$folder/presensi-$today.json";
        $error_log = "$folder/errorLog.txt";

        $json_content = [];
        if (file_exists($json_file)) {
            $json_content = json_decode(file_get_contents($json_file), true) ?? [];
        }
        if (!isset($json_content['data']))   $json_content['data']   = [];
        if (!isset($json_content['recent'])) $json_content['recent'] = [];

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($data['data'] as $item) {
            if (!is_array($item)) continue;

            $nokartu    = trim($item['i'] ?? '');
            $nomorinduk = trim($item['n'] ?? '');
            $t          = trim($item['t'] ?? '');
            $tm         = trim($item['tm'] ?? '');
            $tp         = trim($item['tp'] ?? '');
            $s          = strtoupper(trim($item['s'] ?? ''));

            if (!$nokartu || !$nomorinduk || !$t || !$s) {
                $errors[] = "$meta_timestamp: Data tidak lengkap untuk NIS: $nomorinduk";
                continue;
            }

            // Parse sesi masuk/pulang
            $sm = '';
            $sp = '';
            foreach (str_split($s) as $ch) {
                if (in_array($ch, ['M', 'T']) && $sm === '') $sm = $ch;
                if (in_array($ch, ['C', 'P']) && $sp === '') $sp = $ch;
            }

            $tanggal      = substr($t, 0, 10);
            $jam          = substr($t, 11, 8);
            $tanggalmasuk = !empty($tm) ? substr($tm, 0, 10) : $tanggal;
            $jammasuk     = !empty($tm) ? substr($tm, 11, 8) : '00:00:00';
            $jampulang    = !empty($tp) ? substr($tp, 11, 8) : '00:00:00';

            // Cari data siswa
            $siswa = DB::table('datasiswa')->where('nokartu', $nokartu)->first();
            if (!$siswa) {
                $errors[] = "$meta_timestamp: Kartu tidak terdaftar: $nokartu / $nomorinduk";
                $skipped++;
                continue;
            }

            $nama  = $siswa->nama  ?? '0';
            $info  = $siswa->kelas ?? '0';
            $kode  = $siswa->kode  ?? '0';

            // Cek existing
            $exist = DB::table('datapresensi')
                ->where('nokartu', $nokartu)
                ->where('tanggal', $tanggal)
                ->first();

            $json_entry = null;

            if ($exist) {
                $wmasuk_old  = $exist->waktumasuk  ?: '0';
                $wpulang_old = $exist->waktupulang ?: '0';
                $doUpdate    = false;

                if (in_array($sm, ['M', 'T'])) {
                    if ($wmasuk_old === '0' || strtotime($jam) < strtotime($wmasuk_old)) $doUpdate = true;
                } elseif (in_array($sp, ['C', 'P'])) {
                    if ($wpulang_old === '0' || strtotime($jam) > strtotime($wpulang_old)) $doUpdate = true;
                }

                if ($doUpdate) {
                    $wmasuk_new  = in_array($sm, ['M', 'T']) ? $jammasuk     : $wmasuk_old;
                    $kmasuk_new  = in_array($sm, ['M', 'T']) ? $sm           : ($exist->ketmasuk  ?: '0');
                    $wpulang_new = in_array($sp, ['C', 'P']) ? $jampulang    : $wpulang_old;
                    $kpulang_new = in_array($sp, ['C', 'P']) ? $sp           : ($exist->ketpulang ?: '0');
                    $diff_a      = $this->selisihWaktu($waktumasuk,  $wmasuk_new);
                    $diff_b      = $this->selisihWaktu($waktupulang, $wpulang_new);

                    DB::table('datapresensi')
                        ->where('nokartu', $nokartu)
                        ->where('tanggal', $tanggal)
                        ->update([
                            'waktumasuk'  => $wmasuk_new,
                            'ketmasuk'    => $kmasuk_new,
                            'a_time'      => $diff_a,
                            'waktupulang' => $wpulang_new,
                            'ketpulang'   => $kpulang_new,
                            'b_time'      => $diff_b,
                            'updated_at'  => $t,
                        ]);
                    $updated++;
                    $statusGabungan = trim(($kmasuk_new !== '0' ? $kmasuk_new : '') . ($kpulang_new !== '0' ? $kpulang_new : ''));
                    $json_entry = compact('nokartu', 'nomorinduk', 'nama', 'info', 'kode', 'tanggal') + ['wmasuk' => $wmasuk_new, 'wpulang' => $wpulang_new, 'status' => $statusGabungan];
                } else {
                    $skipped++;
                }
            } else {
                $wmasuk    = in_array($sm, ['M', 'T']) ? $jammasuk  : '0';
                $ketmasuk  = in_array($sm, ['M', 'T']) ? $sm        : '0';
                $wpulang   = in_array($sp, ['C', 'P']) ? $jampulang : '0';
                $ketpulang = in_array($sp, ['C', 'P']) ? $sp        : '0';
                $diff_a    = $this->selisihWaktu($waktumasuk,  $wmasuk);
                $diff_b    = $this->selisihWaktu($waktupulang, $wpulang);

                $nodevice_bind = is_array($meta_nodevice)   ? ($meta_nodevice[0]   ?? '0') : $meta_nodevice;
                $mac_bind      = is_array($meta_macAddress) ? ($meta_macAddress[0] ?? '0') : $meta_macAddress;

                DB::table('datapresensi')->insert([
                    'nokartu'     => $nokartu,
                    'nomorinduk'  => $nomorinduk,
                    'nama'        => $nama,
                    'info'        => $info,
                    'foto'        => '0',
                    'waktumasuk'  => $wmasuk,
                    'ketmasuk'    => $ketmasuk,
                    'a_time'      => $diff_a,
                    'waktupulang' => $wpulang,
                    'ketpulang'   => $ketpulang,
                    'b_time'      => $diff_b,
                    'tanggal'     => $tanggal,
                    'keterangan'  => '0',
                    'updated_at'  => $t,
                    'kode'        => $kode,
                    'infodevice'  => $nodevice_bind,
                    'infodevice2' => $mac_bind,
                ]);
                $inserted++;
                $statusGabungan = trim(($ketmasuk !== '0' ? $ketmasuk : '') . ($ketpulang !== '0' ? $ketpulang : ''));
                $json_entry = compact('nokartu', 'nomorinduk', 'nama', 'info', 'kode', 'tanggal') + ['wmasuk' => $wmasuk, 'wpulang' => $wpulang, 'status' => $statusGabungan];
            }

            // Update JSON backup
            if ($json_entry) {
                $found = false;
                foreach ($json_content['data'] as &$ex) {
                    if (($ex['nokartu'] ?? '') === $nokartu && ($ex['tanggal'] ?? '') === $tanggal) {
                        $ex = $json_entry;
                        $found = true;
                        break;
                    }
                }
                unset($ex);
                if (!$found) $json_content['data'][] = $json_entry;
            }
        }

        // Update recent di JSON backup
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

        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $regDevice = DB::table('reg_device')->where('no_device', $meta_nodevice)->first();
        if ($regDevice) {
            $RUANG = $regDevice->kode;
        } elseif (preg_match('/^IM\d+/i', $meta_nodevice)) {
            $RUANG = 'Izin Mens';
        } elseif (preg_match('/^M0*(\d+)/i', $meta_nodevice, $m)) {
            $RUANG = 'Masjid ' . (int)$m[1];
        } else {
            $RUANG = 'Masjid 3';
        }

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
