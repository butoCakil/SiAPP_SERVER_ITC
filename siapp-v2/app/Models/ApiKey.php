<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'api';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kode_api', 'info_api', 'jenis', 'masaberlaku', 'status',
    ];

    protected $casts = [
        'masaberlaku' => 'date',
    ];

    public function isValid(): bool
    {
        if ($this->status !== 'aktif') return false;
        if ($this->masaberlaku && $this->masaberlaku->isPast()) return false;
        return true;
    }

    public static function findDeviceToken(string $key): ?self
    {
        return self::where('kode_api', $key)
            ->where('jenis', 'device_token')
            ->first();
    }

    public static function findSimToken(string $key): ?self
    {
        return self::where('kode_api', $key)
            ->where('jenis', 'sim_token')
            ->first();
    }
}
