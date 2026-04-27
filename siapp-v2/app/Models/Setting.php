<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'statusnya';
    protected $primaryKey = 'mode';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'mode', 'wa', 'wta', 'wtp', 'wtp_jumat', 'wp', 'wp_jumat',
        'hari_kerja', 'tingkat_aktif', 'auto_mode', 'waktumasuk', 'waktupulang', 'info',
    ];

    protected $casts = [
        'tingkat_aktif' => 'array',
    ];
}
