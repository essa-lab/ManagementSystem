<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class PasswordResetNotification extends Notification implements ShouldQueue
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
        $resetUrl = $this->frontendResetUrl . '/update-password?token=' . $this->token;

        return (new MailMessage)
            ->subject('Update Password Notification')
            ->view('emails.updatePassword', [
                'resetUrl' => $resetUrl,
                'expireMinutes' => config('auth.passwords.users.expire')
            ]);
    }
}
