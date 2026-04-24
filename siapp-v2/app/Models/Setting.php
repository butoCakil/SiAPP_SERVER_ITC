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
        'mode', 'wa', 'wta', 'wtp', 'wp',
        'waktumasuk', 'waktupulang', 'info',
    ];
}
