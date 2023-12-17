<?php

namespace App\Events;

use App\Http\Resources\Message\MessageBroadcastResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public \App\Models\Message $message
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('store-message-event-' . $this->message->chat_id . '-chat'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'store-message-event';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => MessageBroadcastResource::make($this->message)->resolve(),
        ];
    }
}
