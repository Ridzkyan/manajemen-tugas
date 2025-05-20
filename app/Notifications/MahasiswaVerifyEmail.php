<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\MailMessage;

class MahasiswaVerifyEmail extends BaseVerifyEmail
{
    /**
     * Dapatkan URL verifikasi kustom.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'mahasiswa.email-verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Kirim email verifikasi ke mahasiswa.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'mahasiswa.email-verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verifikasi Email Kamu')
            ->greeting('Hello!')
            ->line('Silakan klik tombol di bawah untuk verifikasi email kamu.')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Jika kamu tidak membuat akun, abaikan email ini.')
            ->salutation('Regards, TaskFlow');
    }
}
