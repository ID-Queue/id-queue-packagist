<?php

namespace IdQueue\IdQueuePackagist\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $groupId;

    public function __construct($message, $groupId)
    {
        $this->message = $message;
        $this->groupId = $groupId;
    }

    // Use a presence channel instead of a private channel
    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PresenceChannel('group.'.$this->groupId);
    }

    public function broadcastAs()
    {
        return 'PrivateMessageEvent';
    }
}
