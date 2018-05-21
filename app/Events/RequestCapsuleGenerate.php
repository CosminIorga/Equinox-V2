<?php

namespace Equinox\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestCapsuleGenerate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The reference date used to create a capsule
     * @var Carbon
     */
    protected $referenceDate;

    /**
     * RequestCapsuleGenerate constructor.
     * @param Carbon $referenceDate
     */
    public function __construct(Carbon $referenceDate)
    {
        $this->referenceDate = $referenceDate;
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
     * Get reference date
     * @return Carbon
     */
    public function getReferenceDate(): Carbon
    {
        return $this->referenceDate;
    }
}
