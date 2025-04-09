<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class CustomEmailResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $frontendResetUrl;
    public $email;

    public function __construct($email,$token, $frontendResetUrl = null)
    {
        $this->token = $token;
        $this->email = $email;
        $this->frontendResetUrl = $frontendResetUrl ?? config('app.frontend_url');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = $this->frontendResetUrl . '/update-email?token=' . $this->token;

        $locale = $notifiable->locale ?? 'ku';

        App::setLocale($locale);


        if($locale != 'ku'){
            $resetUrl = $this->frontendResetUrl .'/'. $locale.'/activate-account?token=' . $this->token;

        }else{
            $resetUrl = $this->frontendResetUrl . '/activate-account?token=' . $this->token;


        }
        return (new MailMessage)
            ->subject(__('messages.email_title_email'))
            ->view('emails.updateEmail', [
                'resetUrl' => $resetUrl,
                'expireMinutes' => config('auth.passwords.users.expire')
            ]);
    }
}
