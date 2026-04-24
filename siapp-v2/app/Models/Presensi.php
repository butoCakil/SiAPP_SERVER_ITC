<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'datapresensi';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nokartu', 'nomorinduk', 'nama', 'info', 'foto',
        'waktumasuk', 'ketmasuk', 'a_time',
        'waktupulang', 'ketpulang', 'b_time',
        'tanggal', 'keterangan', 'kode',
        'infodevice', 'infodevice2',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'updated_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nokartu', 'nokartu');
    }
}
