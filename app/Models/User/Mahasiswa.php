<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Events\Verified;
use App\Notifications\MahasiswaVerifyEmail;
use App\Models\Kelas\Kelas;

class Mahasiswa extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    // Gunakan guard mahasiswa
    protected $guard = 'mahasiswa';

    // Tabel yang digunakan
    protected $table = 'users';

    // Field yang dapat diisi
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'kode_unik',
        'is_online',
        'last_login_at',
        'email_verified_at',
    ];

    // Field yang disembunyikan saat serialisasi
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casting
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi ke Kelas (many-to-many).
     */
    public function kelasMahasiswa()
    {
        return $this->belongsToMany(
            Kelas::class,
            'kelas_mahasiswa',
            'mahasiswa_id',
            'kelas_id'
        );
    }

    /**
     * Cek apakah email sudah diverifikasi.
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Tandai email sebagai telah diverifikasi.
     */
    public function markEmailAsVerified()
    {
        if (! $this->hasVerifiedEmail()) {
            $this->email_verified_at = now();
            $this->save();

            event(new Verified($this)); // Penting untuk trigger event
            return true; // Wajib dikembalikan agar EmailVerificationRequest->fulfill() tahu sukses
        }

        return false;
    }

    /**
     * Diperlukan oleh sistem verifikasi Laravel.
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Kirim notifikasi verifikasi email.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new MahasiswaVerifyEmail);
    }
}
