<?php

namespace Equinox\Events;

use Equinox\Models\Capsule\Capsule;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestCapsuleSave
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The capsule template used to persist it in DB
     * @var Capsule
     */
    protected $capsule;

    /**
     * RequestCapsuleSave constructor.
     * @param Capsule $capsule
     */
    public function __construct(Capsule $capsule)
    {
        $this->capsule = $capsule;
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
     * @return Capsule
     */
    public function getCapsule(): Capsule
    {
        return $this->capsule;
    }
}
