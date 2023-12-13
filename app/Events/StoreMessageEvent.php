<?php

namespace App\Events;

use App\Http\Resources\Message\MessageResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreMessageEvent implements ShouldBroadcast
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
            new Channel('store-message-event'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'store-message-event';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => MessageResource::make($this->message)->resolve(),
        ];
    }
}
