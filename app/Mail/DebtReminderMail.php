<?php

namespace App\Mail;

use App\Models\Debt;
use App\Models\DebtReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DebtReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;
    public $debt;

    public function __construct(DebtReminder $reminder)
    {
        $this->reminder = $reminder;
        $this->debt = $reminder->debt;

		//dd($this->reminder);
    }

    public function build()
    {
        return $this->from(
                    config('mail.from.address', 'noreply@everesiyedefteri.com.tr'),
                    config('mail.from.name', 'Everesiye Defteri')
                )
                ->subject('Ödeme Hatırlatması - ')
                ->markdown('emails.debt_reminder')
                ->with([
                    'reminder' => $this->reminder,
                    'debt' => $this->debt,
                ]);
    }
}
