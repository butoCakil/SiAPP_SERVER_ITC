<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiEvent extends Model
{
    protected $table = 'presensiEvent';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nokartu', 'nis', 'ruang', 'mulai',
        'selesai', 'jam', 'tanggal', 'keterangan',
    ];
}
