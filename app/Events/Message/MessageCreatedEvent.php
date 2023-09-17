<?php

namespace App\Events\Message;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageCreatedEvent implements ShouldBroadcast {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
    * @var Message
    */
    private $message;

    /**
    * Create a new event instance.
    * @param Message $message
    */
    public function __construct(Message $message) {
        $this->message = $message;
    }

    /**
    * Get the channels the event should broadcast on.
    *
    * @return array<int, \Illuminate\Broadcasting\Channel>
    */
    public function broadcastOn(): array {
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
        return 'MessageCreatedEvent';
    }

    /**
    * The event's broadcast name.
    *
    * @return array
    */
    public function broadcastWith() {
        return [
            'message' => $this->message->toArray()
        ];
    }
}
