<?php

namespace App\Events;

use App\Models\LiveMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class LiveMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public LiveMessage $message) {}

    public function broadcastOn(): array
    {
        return [new Channel('live.course.' . $this->message->course_id)];
    }

    public function broadcastAs(): string
    {
        return 'LiveMessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'author' => $this->message->author?->only(['id', 'name']),
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}
