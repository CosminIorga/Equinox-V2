<?php

namespace Equinox\Listeners;

use Equinox\Events\RequestCapsuleGeneration;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateCapsule
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RequestCapsuleGeneration  $event
     * @return void
     */
    public function handle(RequestCapsuleGeneration $event)
    {
        //
    }
}
