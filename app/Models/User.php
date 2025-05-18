<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kelas;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kode_unik',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi many-to-many dengan model Kelas
     */
    public function kelasMahasiswa()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mahasiswa', 'mahasiswa_id', 'kelas_id');
    }

    /**
     * 
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmail {
            /**
             * 
             */
            protected function verificationUrl($notifiable)
            {
                return URL::temporarySignedRoute(
                    'mahasiswa.email-verification.verify',
                    now()->addMinutes(60),
                    ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
                );
            }

            /**
             * Build email verifikasi dengan link yang sudah digenerate
             */
            public function toMail($notifiable)
            {
                return (new MailMessage)
                    ->subject('Verifikasi Email Kamu')
                    ->line('Klik tombol di bawah untuk verifikasi email kamu.')
                    ->action('Verifikasi Email', $this->verificationUrl($notifiable))
                    ->line('Jika kamu tidak membuat akun, abaikan email ini.');
            }
        });
    }
}
