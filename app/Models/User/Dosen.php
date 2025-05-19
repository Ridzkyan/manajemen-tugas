<?php

namespace App\Models\User;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Dosen extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $table = 'users';
    protected $guard = 'dosen';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'kode_unik', 'is_online', 'last_login_at'
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function kelas()
    {
        return $this->hasMany(\App\Models\Kelas::class, 'dosen_id');
    }
}

