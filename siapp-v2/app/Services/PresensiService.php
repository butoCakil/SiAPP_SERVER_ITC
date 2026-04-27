<?php

namespace App\Services;

use App\Models\RegDevice;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class PresensiService
{
    // Daftar kode pesan dan keterangannya
    private array $pesanMap = [
        '404'  => 'ERROR!--Method Not Allowed.',
        '405'  => 'ERROR!--REQ TIDAK DI IJINKAN',
        '406'  => 'ERROR!--CHIP TIDAK TERDAFTAR',
        '407'  => 'ERROR!--DEVICE TIDAK SESUAI',
        '505'  => 'ERROR!--DATABASE SERVER',
        'IDTT' => 'Kartu ID ini--belum terdaftar',
        'HLTM' => '--Hari ini Libur',
        'TBPS' => '--Tidak bisa melakukan presensi sekarang.',
        'BMPM' => '--Berhasil Presensi',
        'SMPM' => '--Anda Sudah melakukan Presensi',
        'MMMM' => '--Sudah presensi dengan keterangan',
        'PPBH' => '--Berhasil Presensi Pulang',
        'PLAW' => '--Pulang lebih awal',
        'SAPP' => '--Sudah melakukan presensi pulang',
        'PPPP' => '--Pulang dengan keterangan',
        'PKBD' => '--Berhasil Presensi Kelas',
        'TAKS' => '--Tidak Ada KBM',
        'BMPE' => '--Berhasil Presensi -Mulai-',
        'BPSE' => '--Berhasil Presensi -Selesai-',
        'BPEB' => '--Berhasil Presensi -Mulai Baru-',
        'BMIJ' => '--Ijin Berhasil',
    ];

    /** @param array $input */
    public function prosesTag(array $input): array
    {
        $nokartu  = strtoupper(trim($input['nokartu']  ?? ''));
        $idchip   = trim($input['idchip']   ?? '');
        $nodevice = trim($input['nodevice'] ?? '');
        $ipa      = trim($input['ipa']      ?? '');

        date_default_timezone_set('Asia/Jakarta');
        $jam     = date('H:i:s');
        $tanggal = date('Y-m-d');

        // ── Validasi input dasar ──
        if (!$nokartu || !$idchip || !$nodevice) {
            return $this->buatRespon('404', $idchip, $nodevice, $nokartu);
        }

        // ── Validasi chip_id + no_device di reg_device ──
        $device = DB::table('reg_device')
            ->where('chip_id', $idchip)
            ->where('no_device', $nodevice)
            ->where('status', 'terdaftar')
            ->first();

        if (!$device) {
            // Cek apakah chip_id ada tapi no_device tidak cocok
            $chipAda = DB::table('reg_device')->where('chip_id', $idchip)->exists();
            return $this->buatRespon($chipAda ? '407' : '406', $idchip, $nodevice, $nokartu);
        }

        $kodeDevice = $device->kode; // GATE, MASJID, EVENT, dll

        // ── Ambil setting waktu ──
        $setting = DB::table('statusnya')->first();

        if (!$setting) {
            // return $this->buatResponNama('505', $nama, $idchip, $nodevice, $nokartu);
            return $this->buatRespon('505', $idchip, $nodevice, $nokartu);
        }

        $mode        = (int) $setting->mode;
        $waktumasuk  = $setting->waktumasuk;
        $waktupulang = $setting->waktupulang;
        $wa          = $setting->wa;   // batas masuk tepat waktu
        $wta         = $setting->wta;  // batas masuk toleransi
        $wtp         = $setting->wtp;  // batas pulang toleransi
        $wp          = $setting->wp;   // batas pulang

        // ── Cek kalender libur (via liburnas.json) ──
        $jsonPath = base_path('../beranda/app/liburnas.json');
        if (file_exists($jsonPath)) {
            $liburnas = json_decode(file_get_contents($jsonPath), true);
            $keyTanggal = date('Ymd', strtotime($tanggal));
            if (isset($liburnas[$keyTanggal])) {
                return $this->buatRespon('HLTM', $idchip, $nodevice, $nokartu);
            }
        }

        // ── Cek siswa ──
        $siswa = DB::table('datasiswa')
            ->where('nokartu', $nokartu)
            ->first();

        if (!$siswa) {
            return $this->buatRespon('IDTT', $idchip, $nodevice, $nokartu);
        }

        // ── Mode 0 = presensi ditutup ──
        if ($mode === 0) {
            return $this->buatResponNama('TBPS', $siswa->nama, $idchip, $nodevice, $nokartu);
        }

        $nama        = $siswa->nama;
        $noReg       = $siswa->nis;
        $kode        = $siswa->kode;
        $keterangan  = $siswa->keterangan ?? '';

        // ── Routing berdasarkan kode device ──
        if ($kodeDevice === 'MASJID') {
            return $this->prosesMasjid($nokartu, $noReg, $nama, $idchip, $nodevice, $jam, $tanggal, $kodeDevice);
        }

        if ($kodeDevice === 'EVENT') {
            return $this->prosesEvent($nokartu, $noReg, $nama, $idchip, $nodevice, $jam, $tanggal, $kodeDevice);
        }

        // ── GATE: presensi masuk (mode 1) ──
        if ($mode === 1) {
            return $this->prosesGateMasuk(
                $nokartu, $noReg, $nama, $kode, $keterangan,
                $idchip, $nodevice, $jam, $tanggal,
                $wa, $wta, $waktumasuk, $kodeDevice
            );
        }

        // ── GATE: presensi pulang (mode 2) ──
        if ($mode === 2) {
            return $this->prosesGatePulang(
                $nokartu, $nama, $keterangan,
                $idchip, $nodevice, $jam, $tanggal,
                $waktupulang, $kodeDevice
            );
        }

        return $this->buatRespon('404', $idchip, $nodevice, $nokartu);
    }

    // ── Presensi Masuk (GATE mode 1) ──
    private function prosesGateMasuk(
        string $nokartu, string $noReg, string $nama,
        string $kode, string $keterangan,
        string $idchip, string $nodevice,
        string $jam, string $tanggal,
        string $wa, string $wta, string $waktumasuk,
        string $kodeDevice
    ): array {
        $sudahMasuk = DB::table('datapresensi')
            ->where('nokartu', $nokartu)
            ->where('tanggal', $tanggal)
            ->whereNotNull('waktumasuk')
            ->exists();

        if ($sudahMasuk) {
            $pesan = $keterangan ? 'MMMM' : 'SMPM';
            return $this->buatResponNama($pesan, $nama, $idchip, $nodevice, $nokartu);
        }

        // Tentukan keterlambatan
        if ($jam <= $wa) {
            $ket = 'TW';
            $selisih = '0';
        } elseif ($jam <= $wta) {
            $ket = 'TL';
            $selisih = $this->selisihWaktu($wa, $jam);
        } else {
            $ket = 'TLT';
            $selisih = $this->selisihWaktu($wa, $jam);
        }

        try {
            DB::table('datapresensi')->insert([
                'nokartu'     => $nokartu,
                'tanggal'     => $tanggal,
                'nomorinduk'  => $noReg,
                'nama'        => $nama,
                'info'        => $kode,
                'waktumasuk'  => $jam,
                'ketmasuk'    => $ket,
                'a_time'      => $selisih,
                'keterangan'  => $keterangan ?: null,
                'updated_at'  => now(),
                'kode'        => $kode,
                'infodevice'  => $kodeDevice,
                'infodevice2' => $nodevice,
            ]);

            return $this->buatResponNama('BMPM', $nama, $idchip, $nodevice, $nokartu);
        } catch (\Exception $e) {
            return $this->buatResponNama('505', $nama, '505', $idchip, $nodevice, $nokartu);
        }
    }

    // ── Presensi Pulang (GATE mode 2) ──
    private function prosesGatePulang(
        string $nokartu, string $nama, string $keterangan,
        string $idchip, string $nodevice,
        string $jam, string $tanggal,
        string $waktupulang, string $kodeDevice
    ): array {
        $sudahPulang = DB::table('datapresensi')
            ->where('nokartu', $nokartu)
            ->where('tanggal', $tanggal)
            ->whereNotNull('waktupulang')
            ->exists();

        if ($sudahPulang) {
            return $this->buatResponNama('SAPP', $nama, $idchip, $nodevice, $nokartu);
        }

        if ($keterangan) {
            return $this->buatResponNama('PPPP', $nama, $idchip, $nodevice, $nokartu);
        }

        if ($jam <= $waktupulang) {
            $ket     = 'PA';
            $pesan   = 'PLAW';
            $selisih = $this->selisihWaktu($jam, $waktupulang);
        } else {
            $ket     = 'PLG';
            $pesan   = 'PPBH';
            $selisih = $this->selisihWaktu($waktupulang, $jam);
        }

        try {
            DB::table('datapresensi')
                ->where('nokartu', $nokartu)
                ->where('tanggal', $tanggal)
                ->update([
                    'waktupulang' => $jam,
                    'ketpulang'   => $ket,
                    'b_time'      => $selisih,
                    'infodevice2' => $nodevice,
                    'updated_at'  => now(),
                ]);

            return $this->buatResponNama($pesan, $nama, $idchip, $nodevice, $nokartu);
        } catch (\Exception $e) {
            return $this->buatResponNama('505', $nama, $idchip, $nodevice, $nokartu);
        }
    }

    // ── Presensi Masjid ──
    private function prosesMasjid(
        string $nokartu, string $noReg, string $nama,
        string $idchip, string $nodevice,
        string $jam, string $tanggal, string $kodeDevice
    ): array {
        $batasDzuhurMulai  = '11:45:00';
        $batasDzuhurSelesai = '14:30:00';
        $batasAsharMulai   = '14:30:01';
        $batasAsharSelesai = '17:00:00';

        $now = strtotime($jam);
        $fase = null;

        if ($now >= strtotime($batasDzuhurMulai) && $now <= strtotime($batasDzuhurSelesai)) {
            $fase = 'DZUHUR';
        } elseif ($now >= strtotime($batasAsharMulai) && $now <= strtotime($batasAsharSelesai)) {
            $fase = 'ASHAR';
        }

        if (!$fase) {
            return $this->buatResponNama('TBPS', $nama, $idchip, $nodevice, $nokartu);
        }

        $sudahAda = DB::table('presensiEvent')
            ->where('nokartu', $nokartu)
            ->where('tanggal', $tanggal)
            ->where('keterangan', $fase)
            ->exists();

        if ($sudahAda) {
            return $this->buatResponNama('SMPM', $nama, $idchip, $nodevice, $nokartu);
        }

        try {
            DB::table('presensiEvent')->insert([
                'nokartu'    => $nokartu,
                'nis'        => $noReg,
                'ruang'      => $kodeDevice,
                'mulai'      => $jam,
                'jam'        => $jam,
                'tanggal'    => $tanggal,
                'keterangan' => $fase,
            ]);

            return $this->buatResponNama('BMPE', $nama, $idchip, $nodevice, $nokartu);
        } catch (\Exception $e) {
            return $this->buatResponNama('505', $nama, $idchip, $nodevice, $nokartu);
        }
    }

    // ── Presensi Event ──
    private function prosesEvent(
        string $nokartu, string $noReg, string $nama,
        string $idchip, string $nodevice,
        string $jam, string $tanggal, string $kodeDevice
    ): array {
        $existing = DB::table('presensiEvent')
            ->where('nokartu', $nokartu)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$existing) {
            try {
                DB::table('presensiEvent')->insert([
                    'nokartu'    => $nokartu,
                    'nis'        => $noReg,
                    'ruang'      => $kodeDevice,
                    'mulai'      => $jam,
                    'jam'        => $jam,
                    'tanggal'    => $tanggal,
                    'keterangan' => null,
                ]);
                return $this->buatResponNama('BMPE', $nama, $idchip, $nodevice, $nokartu);
            } catch (\Exception $e) {
                return $this->buatResponNama('505', $nama, $idchip, $nodevice, $nokartu);
            }
        }

        if (!$existing->selesai) {
            try {
                DB::table('presensiEvent')
                    ->where('id', $existing->id)
                    ->update(['selesai' => $jam]);
                return $this->buatResponNama('BPSE', $nama, $idchip, $nodevice, $nokartu);
            } catch (\Exception $e) {
                return $this->buatResponNama('505', $nama, $idchip, $nodevice, $nokartu);
            }
        }

        // Sudah ada mulai & selesai → buat baris baru
        try {
            DB::table('presensiEvent')->insert([
                'nokartu'    => $nokartu,
                'nis'        => $noReg,
                'ruang'      => $kodeDevice,
                'mulai'      => $jam,
                'jam'        => $jam,
                'tanggal'    => $tanggal,
                'keterangan' => null,
            ]);
            return $this->buatResponNama('BPEB', $nama, $idchip, $nodevice, $nokartu);
        } catch (\Exception $e) {
            return $this->buatResponNama('505', $nama, $idchip, $nodevice, $nokartu);
        }
    }

    // ── Helper: hitung selisih waktu dalam detik ──
    private function selisihWaktu(string $dari, string $ke): string
    {
        $selisih = abs(strtotime($ke) - strtotime($dari));
        return (string) $selisih;
    }

    // ── Helper: buat respon tanpa nama ──
    private function buatRespon(string $pesan, string $idchip, string $nodevice, string $nokartu): array
    {
        $info = $this->pesanMap[$pesan] ?? 'Unknown';
        return [
            'respon' => [[
                'id'       => $idchip,
                'nodevice' => $nodevice,
                'message'  => $pesan,
                'info'     => $info,
                'nokartu'  => $nokartu,
            ]]
        ];
    }

    // ── Helper: buat respon dengan nama siswa ──
    private function buatResponNama(string $pesan, string $nama, string $idchip, string $nodevice, string $nokartu): array
    {
        $info = $nama . ($this->pesanMap[$pesan] ?? '');
        return [
            'respon' => [[
                'id'       => $idchip,
                'nodevice' => $nodevice,
                'message'  => $pesan,
                'info'     => $info,
                'nokartu'  => $nokartu,
            ]]
        ];
    }
}
