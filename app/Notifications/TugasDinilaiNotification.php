<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Tugas\Tugas;

class TugasDinilaiNotification extends Notification
{
    use Queueable;

    protected $tugas;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Tugas\Tugas $tugas
     */
    public function __construct(Tugas $tugas)
    {
        $this->tugas = $tugas;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tugas Kamu Telah Dinilai')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Tugas "' . $this->tugas->judul . '" telah dinilai oleh dosen.')
            ->line('📝 Nilai: ' . $this->tugas->nilai)
            ->line('💬 Feedback: ' . ($this->tugas->feedback ?? 'Tidak ada'))
            ->action('Lihat Tugas', url('/mahasiswa/dashboard'))
            ->line('Terima kasih atas partisipasimu!');
    }

    /**
     * Get the array representation of the notification (opsional).
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'judul' => $this->tugas->judul,
            'nilai' => $this->tugas->nilai,
            'feedback' => $this->tugas->feedback,
        ];
    }
}
