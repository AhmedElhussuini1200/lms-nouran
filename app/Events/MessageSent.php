<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Api\MessageResource;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(\App\Models\Message $message)
    {

        // $this->message->load('sender');
        $this->message = new MessageResource($message);
        // Load the sender relationship to ensure we have the sender data

        // \Log::info('MessageSent event created', [
        //     'message_id' => $this->message->id,
        //     'sender_id' => $this->message->sender_id,
        //     'receiver_id' => $this->message->receiver_id,
        //     'message' => $this->message->message,
        // ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $senderId = $this->message->sender_id;
        $receiverId = $this->message->receiver_id;
        $ids = [$senderId, $receiverId];
        sort($ids);
        // \Log::info('Broadcasting on channels', [
        //     'receiver_channel' => 'chat.' . $this->message->receiver_id,
        //     'sender_channel' => 'chat.' . $this->message->sender_id,
        // ]);
        return [
            new PrivateChannel("chat.user.{$ids[0]}.user.{$ids[1]}")
        ];


        // return $channels;
    }

    /**
     * The event's broadcast name.
     */
    // public function broadcastAs(): string
    // {
    //     return 'MessageSent';
    // }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        // $data = [
        //     'message' => $this->message->message,
        //     'attachments' => $this->message->message,
        //     'sender_id' => (int)$this->message->sender_id,
        //     'receiver_id' => (int)$this->message->receiver_id,
        //     'sender_name' => $this->message->sender->first_name ?? 'Unknown',
        //     'created_at' => $this->message->created_at->toDateTimeString(),
        // ];

        // \Log::info('Broadcasting data', $data);

        return [
            'success' => true,
            'data' => $this->message,
            'message' => '',
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    // public function broadcastWhen(): bool
    // {
    //     return true;
    // }
}
