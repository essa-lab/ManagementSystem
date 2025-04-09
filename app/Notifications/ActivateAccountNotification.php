<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class ActivateAccountNotification extends Notification implements ShouldQueue
{    
    use Queueable;

    public $patron;
    public $frontendActivateUrl;

    public function __construct($patron, $frontendActivateUrl = null)
    {
        $this->patron = $patron;
        $this->frontendActivateUrl = $frontendActivateUrl ?? config('app.frontend_url');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $locale = $notifiable->locale ?? 'ku';

        App::setLocale($locale);

        if($locale != 'ku'){
            $activateUrl = $this->frontendActivateUrl .'/'. $locale.'/activate-account?token=' . $this->patron->remember_token;

        }else{
            $activateUrl = $this->frontendActivateUrl . '/activate-account?token=' . $this->patron->remember_token;


        }

        return (new MailMessage)
            ->subject(__('messages.welcome'))
            ->view('emails.welcomeEmail', [
                'activateUrl' => $activateUrl,
            ]);
    }
}
