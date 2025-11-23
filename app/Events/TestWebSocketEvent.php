<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestWebSocketEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channelName;
    public $eventName;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct($channelName, $eventName, $data)
    {
        $this->channelName = $channelName;
        $this->eventName = $eventName;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel($this->channelName);
    }

    /**
     * Get the event name to broadcast as.
     */
    public function broadcastAs(): string
    {
        return $this->eventName;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->data;
    }
}
