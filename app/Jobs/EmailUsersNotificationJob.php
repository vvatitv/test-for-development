<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;

class EmailUsersNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $notify;
    protected $label;

    public function __construct($user, $notify, $label = null)
    {
        $this->onQueue('EmailNotification');

        $this->user = $user;
        $this->notify = $notify;
        $this->label = $label;
    }

    public function middleware()
    {
        return [new RateLimited('EmailNotification')];
    }

    public function retryUntil()
    {
        return now()->addDay();
    }

    public function handle()
    {
        if( !empty($this->label) )
        {
            $label = $this->label;

            $haveNofitication = $this->user->notifications->filter(function($notify) use ($label){
                return !empty($notify->data['label']) && $notify->data['label'] == $label;
            });

            if( $haveNofitication->count() == 0 )
            {
                $this->user->notify($this->notify);
            }

        }else{
            $this->user->notify($this->notify);
        }
        
    }
}
