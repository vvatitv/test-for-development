<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Queue\Middleware\RateLimited;

class MessageNotification extends Notification
{
    use Queueable;

    // public $tries = 3;
    
    protected $sender;
    protected $subject;
    protected $message;
    protected $button;
    protected $label;
    protected $converter;
    protected $notification_id;

    public function __construct($sender = null, $subject, $message, $button = null, $notification_id = null, $label = null)
    {
        $this->sender = $sender;
        $this->subject = $subject;
        $this->message = $message;
        $this->button = $button;

        if( !empty($notification_id) )
        {
            $this->notification_id = $notification_id;
        }
        
        $this->label = $label;

        $this->converter = new \League\HTMLToMarkdown\HtmlConverter([
            'header_style' => 'atx',
            'strip_tags' => true
        ]);

        $this->converter->getConfig()->setOption('hard_break', true);
        $this->converter->getConfig()->setOption('bold_style', '__');
        $this->converter->getConfig()->setOption('italic_style', '*');
        $this->converter->getConfig()->setOption('use_autolinks', false);
    }

    public function via($notifiable)
    {
        $arr = [
            'database'
        ];

        if( !$notifiable->unsubscription )
        {
            $arr[] = 'mail';
        }

        return $arr;
    }

    // public function viaQueues()
    // {
    //     return [
    //         'mail' => 'EmailNotification',
    //         'database' => 'DataBaseNotification',
    //     ];
    // }

    public function toMail($notifiable)
    {
        if( empty($this->notification_id) )
        {
            $this->notification_id = $this->id;
        }
        
        // $texts = Str::of($this->converter->convert($this->message))->explode("\n");
        $texts = Str::of($this->message)->explode("\n");

        $Obj = (new MailMessage);
        $Obj->view('mails.default', [
            'subject' => $this->subject,
            'introLines' => $texts,
            'sender' => $this->sender,
            'label' => $this->label,
            'button' => $this->button,
            'notifiable' => $notifiable,
            'notification_id' => $this->notification_id,
        ]);
        $Obj->subject($this->subject);

        if( $texts->count() )
        {
            foreach ($texts as $text)
            {
                $Obj->line($text);
            }
        }
        
        if( !empty($this->button) )
        {
            if( !empty($this->button['url']) )
            {
                $Obj->action($this->button['text'], $this->button['url']);
            }
        }

        return $Obj;
    }

    public function toDatabase($notifiable)
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
            'sender' => $this->sender,
            'label' => $this->label,
            'button' => $this->button,
            'notification_id' => $this->notification_id,
        ];
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
