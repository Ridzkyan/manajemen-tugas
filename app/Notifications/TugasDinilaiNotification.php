<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Tugas\Tugas;
use App\Models\Tugas\PengumpulanTugas;

class TugasDinilaiNotification extends Notification
{
    use Queueable;

    protected $tugas;
    protected $pengumpulan;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Tugas\Tugas $tugas
     * @param \App\Models\Tugas\PengumpulanTugas $pengumpulan
     */
    public function __construct(Tugas $tugas, PengumpulanTugas $pengumpulan)
    {
        $this->tugas = $tugas;
        $this->pengumpulan = $pengumpulan;
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
            ->line('📝 Nilai: ' . ($this->pengumpulan->nilai ?? '-'))
            ->line('💬 Feedback: ' . ($this->pengumpulan->feedback ?? 'Tidak ada'))
            ->action('Lihat Tugas', url('/mahasiswa/dashboard'))
            ->line('Terima kasih atas partisipasimu!');
    }

    /**
     * Get the array representation of the notification (optional).
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'judul' => $this->tugas->judul,
            'nilai' => $this->pengumpulan->nilai,
            'feedback' => $this->pengumpulan->feedback,
        ];
    }
}
