<?php

namespace App\Events\Message;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageLoadEvent implements ShouldBroadcast {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array<App\Models\Message>
     */
    private array $messages = [];

    /**
     * Create a new event instance.
     */
    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messages'),
        ];
    }

    /**
    * The event's broadcast name.
    *
    * @return string
    */
    public function broadcastAs() {
        return 'MessageLoadEvent';
    }

    /**
    * The event's broadcast name.
    *
    * @return array
    */
    public function broadcastWith() {
        return [
            'messages' => $this->messages
        ];
    }
}
