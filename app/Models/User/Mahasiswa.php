<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kelas\Kelas;
use Illuminate\Auth\Events\Verified;
use App\Notifications\MahasiswaVerifyEmail;

class Mahasiswa extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    // Gunakan guard mahasiswa
    protected $guard = 'mahasiswa';

    // Tabel yang digunakan
    protected $table = 'mahasiswas'; 


    // Field yang bisa diisi
    protected $fillable = [
        'name',
        'email',
        'password',
        'kode_unik',
        'is_online',
        'last_login_at',
    ];

    // Field yang disembunyikan saat serialisasi
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casting field
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi Many-to-Many ke Kelas.
     */
    public function kelas()
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
     * Tandai email sebagai sudah diverifikasi.
     */
    public function markEmailAsVerified()
    {
        if (! $this->hasVerifiedEmail()) {
            $this->forceFill([
                'email_verified_at' => now(),
            ])->save();

            event(new Verified($this));
        }

        return true;
    }

    /**
     * Kirim notifikasi verifikasi email ke mahasiswa.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new MahasiswaVerifyEmail); // 
    }
}
