<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class CustomPasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    public $frontendResetUrl;

    public function __construct($token, $frontendResetUrl = null)
    {
        $this->token = $token;

        $this->frontendResetUrl = $frontendResetUrl ?? config('app.frontend_url');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = $this->frontendResetUrl . '/patron/reset-password?token=' . $this->token;

       
        $locale = $notifiable->locale ?? 'ku';

        App::setLocale($locale);

        if($locale != 'ku'){
            $resetUrl = $this->frontendResetUrl .'/'. $locale. '/patron/reset-password?token=' . $this->token;

        }else{
            $resetUrl = $this->frontendResetUrl . '/patron/reset-password?token=' . $this->token;

        }

        if(isset($notifiable->role)){
            $resetUrl = $this->frontendResetUrl . '/staff/reset-password?token=' . $this->token;
        }

        return (new MailMessage)
            ->subject(__('messages.password_title_email'))
            ->view('emails.forgetPassword', [
                'resetUrl' => $resetUrl,
                'expireMinutes' => config('auth.passwords.users.expire')
            ]);
    }
}
