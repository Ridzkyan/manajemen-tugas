<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class MahasiswaVerifyEmail extends BaseVerifyEmail
{
    /**
     * Dapatkan URL verifikasi yang sudah di custom.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'mahasiswa.verification.verify', // 🟢 route yang kamu definisikan untuk mahasiswa
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
