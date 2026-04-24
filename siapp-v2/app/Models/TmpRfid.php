<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmpRfid extends Model
{
    protected $table = 'tmprfid';
    protected $primaryKey = 'nokartu';
    public $incrementing = false;
    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nokartu', 'nokartu_admin', 'nokartu_emanpo',
    ];
}
