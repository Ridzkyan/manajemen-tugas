<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TugasBaruNotification extends Notification
{
    use Queueable;

    protected $tugas;

    public function __construct($tugas)
    {
        $this->tugas = $tugas;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $kelasNama = optional($this->tugas->kelas)->nama_kelas ?? 'kelas';

        return (new MailMessage)
            ->subject('Tugas Baru Telah Diunggah')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Dosen telah mengunggah tugas baru untuk kelas ' . $kelasNama)
            ->line('Judul Tugas: ' . $this->tugas->judul)
            ->line('Deadline: ' . ($this->tugas->deadline
                ? date('d M Y H:i', strtotime($this->tugas->deadline))
                : 'Tidak ditentukan'))
            ->action('Lihat Tugas', route('mahasiswa.kelas.tugas.index', $this->tugas->kelas_id))
            ->line('Segera kerjakan tugas ini sebelum deadline. Semangat!');
    }
}
