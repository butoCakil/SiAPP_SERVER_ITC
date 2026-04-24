<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'dataguru';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $hidden = ['password'];

    protected $fillable = [
        'nokartu', 'nip', 'nama', 'nick', 'status', 'info', 'foto',
        'keterangan', 'tglawaldispo', 'tglakhirdispo', 'docdis',
        't_waktu_telat', 'poin', 'kode', 'jabatan',
        'akses', 'ket_akses', 'saldo',
        'total_transaksi', 'total_belanja',
        'email', 'login', 'tentang',
        'template_pesan', 'level_login',
    ];
}
