<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class OverdueNotification extends Notification implements ShouldQueue
{    
    use Queueable,SerializesModels;

    public $circulation;

    public function __construct($circulation)
    {
        $this->circulation = $circulation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $locale = $notifiable->locale ?? 'en';

        App::setLocale($locale);


        return (new MailMessage)
            ->subject(__('messages.overdue'))
            ->view('emails.overdue', [
                'patronName' => $this->circulation->patron->name,
                'bookTitle' => $this->circulation->resourceCopy->resource->{'title_' . $locale},
                'dueDate' => $this->circulation->due_date->format('Y-m-d'),
            ]);

        }
}
