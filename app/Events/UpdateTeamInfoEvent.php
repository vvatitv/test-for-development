<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateTeamInfoEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($team)
    {
        $this->data = $team;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('team-info-update.' . $this->data['slug']);
    }

    public function broadcastAs()
    {
        return 'TeamInfoUpdateEvent';
    }
}
