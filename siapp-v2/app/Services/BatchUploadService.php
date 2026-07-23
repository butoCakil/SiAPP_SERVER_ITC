<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BatchUploadService
{
    /**
     * Proses batch entry sholat (dari device Masjid/Izin Mens).
     * Dipakai oleh UploadController::sholat() (HTTP) dan AntreanSubscribe (MQTT) --
     * satu sumber logic, supaya hasil dedup+insert konsisten apa pun transportnya.
     *
     * @param array  $items    Array item mentah dari payload (field i,n,s,t)
     * @param string $nodevice Kode device pengirim (untuk deteksi RUANG)
     * @return array{inserted:int, skipped:int, errors:array, insertedEntries:array}
     */
    public function prosesSholat(array $items, string $nodevice): array
    {
        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $insertedEntries = [];  // === BARU: detail per-entry yg di-insert, utk backup/log caller ===

        $regDevice = DB::table('reg_device')->where('no_device', $nodevice)->first();
        if ($regDevice) {
            $RUANG = $regDevice->kode;
        } elseif (preg_match('/^IM\d+/i', $nodevice)) {
            $RUANG = 'Izin Mens';
        } elseif (preg_match('/^M0*(\d+)/i', $nodevice, $m)) {
            $RUANG = 'Masjid ' . (int)$m[1];
        } else {
            $RUANG = 'Masjid 3';
        }

        foreach ($items as $item) {
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

                $insertedEntries[] = [
                    'nokartu'    => $nokartu,
                    'nis'        => $nis,
                    'tanggal'    => $tgl,
                    'jam'        => $j,
                    'keterangan' => $ket,
                    'nodevice'   => $nodevice,
                ];
            }
        }

        return [
            'inserted'        => $inserted,
            'skipped'         => $skipped,
            'errors'          => $errors,
            'insertedEntries' => $insertedEntries,
        ];
    }

    /**
     * Proses batch entry presensi GATE (masuk/pulang).
     * Dipakai oleh UploadController::presensi() (HTTP) dan AntreanSubscribe (MQTT) --
     * satu sumber logic, sama seperti prosesSholat().
     *
     * @param array  $items       Array item mentah dari payload (field i,n,t,tm,tp,s)
     * @param string $nodevice    Kode device pengirim
     * @param string $macAddress  MAC address device (utk kolom infodevice2)
     * @param string $timestamp   Timestamp meta dari payload (utk updated_at & pesan error)
     * @return array{inserted:int, updated:int, skipped:int, errors:array, entries:array}
     */
    public function prosesPresensi(array $items, string $nodevice, string $macAddress, string $timestamp): array
    {
        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $entries = [];  // gabungan insert+update, dipakai caller utk backup JSON

        $setting     = DB::table('statusnya')->first();
        $waktumasuk  = $setting->waktumasuk  ?? '00:00:00';
        $waktupulang = $setting->waktupulang ?? '00:00:00';

        foreach ($items as $item) {
            if (!is_array($item)) continue;

            $nokartu    = trim($item['i'] ?? '');
            $nomorinduk = trim($item['n'] ?? '');
            $t          = trim($item['t'] ?? '');
            $tm         = trim($item['tm'] ?? '');
            $tp         = trim($item['tp'] ?? '');
            $s          = strtoupper(trim($item['s'] ?? ''));

            if (!$nokartu || !$nomorinduk || !$t || !$s) {
                $errors[] = "$timestamp: Data tidak lengkap untuk NIS: $nomorinduk";
                continue;
            }

            $sm = '';
            $sp = '';
            foreach (str_split($s) as $ch) {
                if (in_array($ch, ['M', 'T']) && $sm === '') $sm = $ch;
                if (in_array($ch, ['C', 'P']) && $sp === '') $sp = $ch;
            }

            $tanggal   = substr($t, 0, 10);
            $jam       = substr($t, 11, 8);  // dipakai utk KEPUTUSAN update (bukan nilai disimpan)
            $jammasuk  = !empty($tm) ? substr($tm, 11, 8) : '00:00:00';
            $jampulang = !empty($tp) ? substr($tp, 11, 8) : '00:00:00';

            $siswa = DB::table('datasiswa')->where('nokartu', $nokartu)->first();
            if (!$siswa) {
                $errors[] = "$timestamp: Kartu tidak terdaftar: $nokartu / $nomorinduk";
                $skipped++;
                continue;
            }

            if (($siswa->status ?? 'aktif') !== 'aktif') {
                $errors[] = "$timestamp: Siswa tidak aktif (status={$siswa->status}): $nokartu / {$siswa->nama}";
                $skipped++;
                continue;
            }

            $nama = $siswa->nama  ?? '0';
            $info = $siswa->kelas ?? '0';
            $kode = $siswa->kode  ?? '0';

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
                    $wmasuk_new  = in_array($sm, ['M', 'T']) ? $jammasuk  : $wmasuk_old;
                    $kmasuk_new  = in_array($sm, ['M', 'T']) ? $sm        : ($exist->ketmasuk  ?: '0');
                    $wpulang_new = in_array($sp, ['C', 'P']) ? $jampulang : $wpulang_old;
                    $kpulang_new = in_array($sp, ['C', 'P']) ? $sp        : ($exist->ketpulang ?: '0');
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
                    'infodevice'  => $nodevice,
                    'infodevice2' => $macAddress,
                ]);
                $inserted++;
                $statusGabungan = trim(($ketmasuk !== '0' ? $ketmasuk : '') . ($ketpulang !== '0' ? $ketpulang : ''));
                $json_entry = compact('nokartu', 'nomorinduk', 'nama', 'info', 'kode', 'tanggal') + ['wmasuk' => $wmasuk, 'wpulang' => $wpulang, 'status' => $statusGabungan];
            }

            if ($json_entry) $entries[] = $json_entry;
        }

        return [
            'inserted' => $inserted,
            'updated'  => $updated,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'entries'  => $entries,
        ];
    }

    /**
     * Duplikat dari UploadController::selisihWaktu() -- disalin ke sini supaya
     * BatchUploadService berdiri sendiri, tidak tergantung balik ke controller.
     */
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
}
