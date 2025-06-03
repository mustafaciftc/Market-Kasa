<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DebtReminder extends Notification
{
    use Queueable;

    protected $debt;

    public function __construct($debt)
    {
        $this->debt = $debt;
    }

    public function via($notifiable)
    {
        return ['mail']; // Bildirimi e-posta ile göndermek için
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Borç Hatırlatma')
            ->greeting('Merhaba ' . $notifiable->name)
            ->line('Borç tutarınız: ' . $this->debt->amount . ' ₺')
            ->line('Son ödeme tarihi: ' . $this->debt->due_date)
            ->action('Ödeme Yap', url('/payments'))
            ->line('Ödemenizi zamanında yapmayı unutmayın.');
    }
}
