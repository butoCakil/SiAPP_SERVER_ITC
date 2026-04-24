<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'device_id', 'last_seen', 'offline_since', 'online_since',
        'online', 'fw_version', 'last_setting',
        'last_command', 'last_status', 'info',
    ];

    protected $casts = [
        'online'       => 'boolean',
        'last_seen'    => 'datetime',
        'offline_since'=> 'datetime',
        'online_since' => 'datetime',
        'last_setting' => 'array',
        'last_command' => 'array',
        'last_status'  => 'array',
    ];

    public function logs()
    {
        return $this->hasMany(DeviceLog::class, 'device_id', 'device_id');
    }
}
