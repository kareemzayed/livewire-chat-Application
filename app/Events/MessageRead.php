<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $conversationId;
    public $receiverId;

    public function __construct($conversationId, $receiverId)
    {
        $this->conversationId = $conversationId;
        $this->receiverId = $receiverId;
    }
    public function broadcastWith() {
        return [
            'conversation_id' => $this->conversationId,
            'receiver_id' => $this->receiverId,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->receiverId);
    }
}
