<?php

namespace App\Notifications;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmailNotification extends VerifyEmailBase
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if( static::$toMailCallback )
        {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return (new MailMessage)
            ->subject(Lang::get('Verify Email Address'))
            ->greeting(Lang::get('Hello!'))
            ->line('Вы получили это письмо, так как вас зарегистрировали для участия в конкурсе [«Проектная активация»](' . url(route('home')) . ') в составе команды «Название команды».')
            ->line('')
            ->line('Перейдите по [ссылке](' . $verificationUrl . ') или кликните на кнопку ниже, чтобы подтвердить регистрацию и создать пароль для вашей учетной записи.')
            ->action('Подтвердить регистрацию', $verificationUrl);
    }

    protected function verificationUrl($notifiable)
    {
        if( static::$createUrlCallback )
        {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        return url(route('verification.show.index', $notifiable));

        // return URL::temporarySignedRoute(
        //     'verification.verify',
        //     Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
        //     [
        //         'id' => $notifiable->getKey(),
        //         'hash' => sha1($notifiable->getEmailForVerification()),
        //     ]
        // );
    }
}
