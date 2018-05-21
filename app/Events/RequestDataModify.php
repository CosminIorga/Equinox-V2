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
     * The array containing data to modify
     * @var array
     */
    protected $mapping;

    /**
     * Create a new event instance.
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
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
    public function getMapping(): array
    {
        return $this->mapping;
    }

}
