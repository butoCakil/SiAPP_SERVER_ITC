<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kaldik extends Model
{
    protected $table = 'kaldik';

    protected $fillable = [
        'tanggal',
        'judul',
        'tipe',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Tipe yang menyebabkan presensi tutup otomatis
    public static function isLibur(string $tanggal): bool
    {
        return self::where('tanggal', $tanggal)
            ->whereIn('tipe', ['libur_nasional', 'cuti_bersama', 'libur_semester'])
            ->exists();
    }

    // Ambil semua event pada tanggal tertentu
    public static function getByTanggal(string $tanggal): \Illuminate\Support\Collection
    {
        return self::where('tanggal', $tanggal)->get();
    }

    // Label warna per tipe
    public static function warna(string $tipe): string
    {
        return match ($tipe) {
            'libur_nasional'  => '#e53935',
            'cuti_bersama'    => '#ff6f00',
            'libur_semester'  => '#7b1fa2',
            'kegiatan'        => '#1565c0',
            'daring'          => '#00838f',
            'force_majeure'   => '#424242',
            default           => '#607d8b',
        };
    }
}
