<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class ReturnResourceEmail extends Notification implements ShouldQueue
{    
    use Queueable;



    protected $patron;
    public function __construct($patron)
    {
        $this->patron=$patron;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject(__('messages.return_resource'))
            
            ->view('emails.returnResource', ['patron'=>$this->patron
            ]);
    }
}
