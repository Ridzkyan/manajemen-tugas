<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TugasBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tugas;

    /**
     * Buat instance notifikasi baru.
     *
     * @param  mixed  $tugas
     */
    public function __construct($tugas)
    {
        $this->tugas = $tugas;
    }

    /**
     * Saluran pengiriman notifikasi.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Format email yang dikirim.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tugas Baru dari Dosen')
            ->greeting('Hai ' . $notifiable->name . ' 👋')
            ->line('Ada tugas baru dari dosen: *' . $this->tugas->judul . '*')
            ->line('Tipe: ' . ucfirst($this->tugas->tipe))
            ->line('Deadline: ' . ($this->tugas->deadline ?? 'Tidak ditentukan'))
            ->action('Lihat Tugas', url('/mahasiswa/kelas/' . $this->tugas->kelas_id . '/tugas'))
            ->line('Silakan kerjakan sebelum deadline ya!');
    }

    /**
     * Representasi notifikasi dalam bentuk array.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'judul'     => $this->tugas->judul,
            'tipe'      => $this->tugas->tipe,
            'deadline'  => $this->tugas->deadline,
            'kelas_id'  => $this->tugas->kelas_id,
        ];
    }
}
