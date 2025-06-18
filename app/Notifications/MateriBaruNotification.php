<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MateriBaruNotification extends Notification
{
    use Queueable;

    protected $materi;

    public function __construct($materi)
    {
        $this->materi = $materi;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Materi Baru Telah Diunggah')
                    ->line('Dosen telah mengunggah materi baru pada kelas ' . $this->materi->kelas->nama)
                    ->action('Lihat Materi', url('/mahasiswa/kelas/'.$this->materi->kelas_id.'/materi'))
                    ->line('Segera pelajari materi tersebut ya!');
    }
}
