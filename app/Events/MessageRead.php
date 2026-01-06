<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
  public function __construct(
        public Message $message
    ) {}
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
       return [
            new Channel('chatread.' . $this->message->sender_id)
        ];
    }

       public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'read_by' => $this->message->receiver_id
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }
}
