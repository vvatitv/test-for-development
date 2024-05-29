<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    public function toMail($notifiable)
    {
        if( static::$toMailCallback )
        {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        if( static::$createUrlCallback )
        {
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        } else {
            $url = url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        }

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->greeting(Lang::get('Hello!'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->line('')
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'))
            ->action(Lang::get('Reset Password'), $url);
    }
}
