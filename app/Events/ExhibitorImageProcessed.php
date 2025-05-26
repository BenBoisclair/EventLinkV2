<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Use ShouldBroadcastNow for immediate feedback
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExhibitorImageProcessed implements ShouldBroadcastNow // Implementing ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The ID of the event this exhibitor belongs to in this context.
     * @var int
     */
    public int $eventId;

    /**
     * The ID of the exhibitor whose image was processed.
     *
     * @var int
     */
    public $exhibitorId;

    /**
     * The field name that was updated ('logo_path' or 'banner_path').
     *
     * @var string
     */
    public $fieldName;

    /**
     * The public URL of the processed image.
     *
     * @var string
     */
    public $imageUrl;

    /**
     * Create a new event instance.
     *
     * @param int $eventId
     * @param int $exhibitorId
     * @param string $fieldName
     * @param string $imageUrl
     * @return void
     */
    public function __construct(int $eventId, int $exhibitorId, string $fieldName, string $imageUrl)
    {
        $this->eventId = $eventId;
        $this->exhibitorId = $exhibitorId;
        $this->fieldName = $fieldName;
        $this->imageUrl = $imageUrl;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * We broadcast on a private channel for the specific event.
     * Only users authorized for this event channel will receive the update.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast on a private channel specific to the event
        return [new PrivateChannel('event.' . $this->eventId . '.exhibitors')];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'exhibitor.image.processed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'event_id' => $this->eventId,
            'exhibitor_id' => $this->exhibitorId,
            'field_name' => $this->fieldName,
            'image_url' => $this->imageUrl,
        ];
    }
}
