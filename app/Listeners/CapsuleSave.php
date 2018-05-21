<?php

namespace Equinox\Listeners;

use Equinox\Events\RequestCapsuleSave;
use Equinox\Services\Capsule\CapsuleSaveService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CapsuleSave implements ShouldQueue
{

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'capsule:save';

    /**
     * The capsule service
     * @var CapsuleSaveService
     */
    protected $capsuleSaveService;

    /**
     * Create the event listener.
     * @param CapsuleSaveService $capsuleSaveService
     */
    public function __construct(
        CapsuleSaveService $capsuleSaveService
    ) {
        $this->capsuleSaveService = $capsuleSaveService;
    }

    /**
     * Handle the event.
     *
     * @param  RequestCapsuleSave $event
     * @return void
     */
    public function handle(RequestCapsuleSave $event)
    {
        try {
            $this->capsuleSaveService->saveCapsule($event->getCapsule());
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }
    }
}
