<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'datasiswa';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $hidden = ['password'];

    protected $fillable = [
        'nokartu', 'nis', 'nama', 'nick', 'kelas',
        'foto', 'kelompok', 't_waktu_telat', 'poin',
        'keterangan', 'tglawal', 'tglakhir', 'fotodoc',
        'kode', 'tingkat', 'jur', 'saldo',
        'total_transaksi', 'total_belanja', 'tentang',
        'email', 'login',
    ];

    protected $casts = [
        'tglawal'  => 'date',
        'tglakhir' => 'date',
    ];

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'nokartu', 'nokartu');
    }
}
