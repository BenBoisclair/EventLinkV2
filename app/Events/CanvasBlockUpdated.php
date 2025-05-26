<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Block;

class CanvasBlockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The Block instance.
     *
     * @var \App\Models\Block
     */
    public Block $block;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Block $block
     * @return void
     */
    public function __construct(Block $block)
    {
        $this->block = $block;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\PrivateChannel>
     */
    public function broadcastOn(): array
    {
        // Broadcast on a private channel specific to the website and block
        return [
            new PrivateChannel('canvas-block.' . $this->block->id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'canvas.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'blockId' => (string)$this->block->id,
            'updated_at' => now()->timestamp,
        ];
    }
}
