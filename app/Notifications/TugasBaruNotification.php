<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TugasBaruNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
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
     * Create a new notification instance.
     *
     * @param  mixed  $tugas
     * @return void
     */
    public function __construct($tugas)
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
            ->subject('Tugas Baru dari Dosen')
            ->greeting('Hai ' . $notifiable->name . ' 👋')
            ->line('Dosen telah mengunggah tugas baru: *' . $this->tugas->judul . '*')
            ->line('Tipe: ' . ucfirst($this->tugas->tipe))
            ->line('Deadline: ' . ($this->tugas->deadline ?? 'Tidak ditentukan'))
            ->action('Lihat Tugas', url('/mahasiswa/kelas/' . $this->tugas->kelas_id . '/tugas'))
            ->line('Silakan kerjakan tugas sebelum deadline ya!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'judul' => $this->tugas->judul,
            'tipe' => $this->tugas->tipe,
            'deadline' => $this->tugas->deadline,
            'kelas_id' => $this->tugas->kelas_id
        ];
    }
}
