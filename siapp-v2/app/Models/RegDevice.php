<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegDevice extends Model
{
    protected $table = 'reg_device';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'chip_id', 'no_device', 'kode', 'info_device', 'status',
    ];
}
