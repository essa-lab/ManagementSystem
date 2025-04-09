<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class SeasonEmail extends Notification implements ShouldQueue
{    
    use Queueable;

    public $filePath;


    public function __construct( $filePath)
    {
        $this->filePath = $filePath ;

    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $locale = $notifiable->locale ?? 'en';

        App::setLocale($locale);

        $fullPath = storage_path('app/public/' . $this->filePath);


        return (new MailMessage)
            ->subject(__('messages.season_report'))
            ->attach($fullPath, [
                'as' => 'Season_Report.pdf',
                'mime' => 'application/pdf',
            ])
            ->view('emails.seasonReport');
    }
}
