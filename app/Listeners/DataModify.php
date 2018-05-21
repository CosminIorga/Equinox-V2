<?php

namespace Equinox\Listeners;

use Equinox\Events\RequestDataModify;
use Illuminate\Contracts\Queue\ShouldQueue;

class DataModify implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'data:modify';

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
     * @param  RequestDataModify $event
     * @return void
     */
    public function handle(RequestDataModify $event)
    {
        //dump($event->getMapping());
    }
}
