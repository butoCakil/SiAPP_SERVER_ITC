<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSiswaBaru extends Command
{
    protected $signature   = 'siswa:import-baru {file : Path ke file CSV} {--dry-run : Tampilkan rencana tanpa eksekusi}';
    protected $description = 'Import siswa baru kelas X dari CSV (kolom minimal: Kelas, Nama, NIS)';

    public function handle(): void
    {
        $path   = $this->argument('file');
        $dryRun = (bool) $this->option('dry-run');

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $header = array_map('trim', $header);

        $idxKelas = array_search('Kelas', $header);
        $idxNama  = array_search('Nama', $header);
        $idxNis   = array_search('NIS', $header);

        if ($idxKelas === false || $idxNama === false || $idxNis === false) {
            $this->error('Header CSV harus punya kolom: Kelas, Nama, NIS. Header terbaca: ' . implode(', ', $header));
            fclose($handle);
            return;
        }

        $rencana = [];
        $dupNis  = [];
        $formatSalah = [];
        $baris = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $baris++;
            $kelas = trim($row[$idxKelas] ?? '');
            $nama  = trim($row[$idxNama] ?? '');
            $nis   = trim($row[$idxNis] ?? '');

            if (!$kelas || !$nama || !$nis) {
                $formatSalah[] = "Baris {$baris}: kolom kosong (kelas='{$kelas}', nama='{$nama}', nis='{$nis}')";
                continue;
            }

            // Parse "X AT 1" -> tingkat=X, jur=AT
            $token = preg_split('/\s+/', $kelas);
            if (count($token) < 2 || $token[0] !== 'X') {
                $formatSalah[] = "Baris {$baris}: format kelas tidak dikenali atau bukan kelas X -> '{$kelas}'";
                continue;
            }
            $tingkat = $token[0];
            $jur     = $token[1];

            if (DB::table('datasiswa')->where('nis', $nis)->exists()) {
                $dupNis[] = "NIS {$nis} ({$nama}) sudah ada di datasiswa — DILEWATI";
                continue;
            }

            $rencana[] = [
                'nokartu'    => '',
                'nis'        => $nis,
                'nama'       => strtoupper($nama),
                'nick'       => strtolower($nis),
                'kelas'      => $kelas,
                'status'     => 'aktif',
                'foto'       => 'default.jpg',
                'kelompok'   => '1',
                'kode'       => '',
                'tingkat'    => $tingkat,
                'jur'        => $jur,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        fclose($handle);

        if (!empty($formatSalah)) {
            $this->error('Baris dengan format tidak valid (DILEWATI):');
            foreach ($formatSalah as $m) $this->line("  - {$m}");
        }
        if (!empty($dupNis)) {
            $this->warn('NIS duplikat (DILEWATI):');
            foreach ($dupNis as $m) $this->line("  - {$m}");
        }

        $ringkasan = collect($rencana)->groupBy('kelas')->map(fn($g) => count($g))->sortKeys();
        $this->info('=== RENCANA IMPORT ===');
        foreach ($ringkasan as $kelas => $jumlah) {
            $this->line("  {$kelas}  ({$jumlah} siswa)");
        }
        $this->info('Total siswa akan diimport: ' . count($rencana));

        if ($dryRun) {
            $this->warn('--dry-run aktif, tidak ada data yang disimpan.');
            return;
        }

        if (empty($rencana)) {
            $this->warn('Tidak ada data valid untuk diimport.');
            return;
        }

        if (!$this->confirm('Lanjutkan import ke database?', false)) {
            $this->warn('Dibatalkan.');
            return;
        }

        DB::transaction(function () use ($rencana) {
            foreach (array_chunk($rencana, 100) as $chunk) {
                DB::table('datasiswa')->insert($chunk);
            }
        });

        $this->info('Selesai. ' . count($rencana) . ' siswa baru diimport.');
    }
}
