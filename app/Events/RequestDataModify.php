<?php

namespace Equinox\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestDataModify
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The mapped data
     * @var array
     */
    protected $mapData;

    /**
     * Create a new event instance.
     * @param array $mapData
     */
    public function __construct(array $mapData)
    {
        $this->mapData = $mapData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    /**
     * Mapping getter
     * @return array
     */
    public function getMappedData(): array
    {
        return $this->mapData;
    }
}
