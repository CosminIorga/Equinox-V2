<?php

namespace Equinox\Listeners;

use Equinox\Events\RequestCapsuleSave;
use Equinox\Services\Data\CapsuleService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveCapsule implements ShouldQueue
{

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'saveCapsule';

    /**
     * The capsule service
     * @var CapsuleService
     */
    protected $capsuleService;

    /**
     * Create the event listener.
     * @param CapsuleService $capsuleService
     */
    public function __construct(
        CapsuleService $capsuleService
    ) {
        $this->capsuleService = $capsuleService;
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
            $this->capsuleService->generateCapsule($event->getCapsule());
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }
    }
}
