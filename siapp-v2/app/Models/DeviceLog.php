<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    protected $table = 'device_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // Tidak pakai fillable mass-assignment — log hanya ditulis via DB langsung
    // Query selalu dibatasi: latest()->limit(100)
}
