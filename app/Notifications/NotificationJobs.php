<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificationJobs extends Notification
{
    use Queueable;
    protected $taskassign;


    /**
     * Create a new notification instance.
     */
    public function __construct($taskassign)
    {
        $this->taskassign = $taskassign;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kamu Mendapatkan Tugas Baru')
            ->greeting("Hello " . $this->taskassign->assignee->name)
            ->line('Tugas Perlu dikerjakan pada tanggal ' . formatDate($this->taskassign->assign_date) .
                ' di lokasi ' . $this->taskassign->place)
            ->line('Posisi: ' . $this->taskassign->assignee->position->name)
            ->line('Departemen: ' . $this->taskassign->assignee->department->name)
            ->action('Lihat Tugas', route('assigmentdata.detail', ['assignid' => $this->taskassign->id]))
            ->line('Harap tinjau tugas dan ambil tindakan yang diperlukan.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => "Tugas Kamu " . $this->taskassign->tasktemplate->name . " Tanggal " . formatDate($this->taskassign->assign_date) . " Lokasi " . $this->taskassign->place
        ];
    }
}
