<?php

namespace Equinox\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestDataMap
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * The data that needs to be inserted
     * @var array
     */
    protected $data;

    /**
     * RequestCapsuleSave constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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
     * Getter for capsule
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
