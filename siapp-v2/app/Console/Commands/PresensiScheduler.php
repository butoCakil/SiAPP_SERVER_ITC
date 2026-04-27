<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PresensiScheduler extends Command
{
    protected $signature   = 'presensi:scheduler';
    protected $description = 'Otomatis buka/tutup presensi sesuai jadwal';

    public function handle(): void
    {
        date_default_timezone_set('Asia/Jakarta');

        $setting = DB::table('statusnya')->first();

        if (!$setting) {
            $this->error('Setting tidak ditemukan.');
            return;
        }

        // Jika mode manual, tidak lakukan apapun
        if ((int) $setting->auto_mode === 0) {
            $this->line('[' . now() . '] Mode MANUAL — scheduler tidak aktif.');
            return;
        }

        $now       = now();
        $jam       = $now->format('H:i:s');
        $hari      = (int) $now->format('N'); // 1=Senin, 5=Jumat, 6=Sabtu, 7=Minggu
        $hariKerja = (int) $setting->hari_kerja;
        $isJumat   = $hari === 5;
        $isMinggu  = $hari === 7;
        $isSabtu   = $hari === 6;

        // Cek hari kerja
        if ($isMinggu) {
            $this->line('[' . now() . '] Hari Minggu — presensi libur.');
            $this->setMode(0, $setting);
            return;
        }

        if ($isSabtu && $hariKerja == 5) {
            $this->line('[' . now() . '] Sabtu & 5 hari kerja — presensi libur.');
            $this->setMode(0, $setting);
            return;
        }

        // Tentukan batas waktu berdasarkan hari
        if ($isJumat) {
            $bukaMasuk  = $setting->wa;
            $tutupMasuk = $setting->wta;
            $bukaPulang = $setting->wtp_jumat;
            $tutupAll   = $setting->wp_jumat;
        } else {
            $bukaMasuk  = $setting->wa;
            $tutupMasuk = $setting->wta;
            $bukaPulang = $setting->wtp;
            $tutupAll   = $setting->wp;
        }

        $modeBaru = $setting->mode;

        if ($jam >= $bukaMasuk && $jam < $tutupMasuk) {
            $modeBaru = 1; // MASUK
        } elseif ($jam >= $tutupMasuk && $jam < $bukaPulang) {
            $modeBaru = 0; // TUTUP (jeda antara masuk dan pulang)
        } elseif ($jam >= $bukaPulang && $jam < $tutupAll) {
            $modeBaru = 2; // PULANG
        } else {
            $modeBaru = 0; // TUTUP
        }

        if ($modeBaru !== (int) $setting->mode) {
            $this->setMode($modeBaru, $setting);
            $label = match($modeBaru) {
                1 => 'MASUK DIBUKA',
                2 => 'PULANG DIBUKA',
                0 => 'DITUTUP',
            };
            $this->info('[' . now() . '] Mode berubah → ' . $label);
        } else {
            $this->line('[' . now() . '] Mode tetap: ' . $setting->mode);
        }
    }

    private function setMode(int $mode, object $setting): void
    {
        if ((int) $setting->mode !== $mode) {
            DB::table('statusnya')->update(['mode' => $mode]);
        }
    }
}