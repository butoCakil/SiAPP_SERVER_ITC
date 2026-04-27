<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table      = 'admin';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'username', 'email', 'password',
        'status', 'wa', 'fb', 'ig', 'foto',
    ];

    protected $hidden = ['password'];

    public function verifyPassword(string $input): bool
    {
        return md5($input) === $this->password;
    }
}