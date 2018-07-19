<?php

namespace Equinox\Listeners;

use Equinox\Events\RequestCapsuleGenerate;
use Equinox\Events\RequestCapsuleSave;
use Equinox\Services\Capsule\CapsuleGenerateService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CapsuleGenerate implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'capsule:generate';

    /**
     * The capsule service
     * @var CapsuleGenerateService
     */
    protected $capsuleGenerateService;

    /**
     * Create the event listener.
     * @param CapsuleGenerateService $capsuleGenerateService
     */
    public function __construct(CapsuleGenerateService $capsuleGenerateService)
    {
        $this->capsuleGenerateService = $capsuleGenerateService;
    }

    /**
     * Handle the event
     * @param RequestCapsuleGenerate $event
     * @throws \Equinox\Exceptions\ModelException
     */
    public function handle(RequestCapsuleGenerate $event)
    {
        $capsules = $this->capsuleGenerateService->createCapsulesByReferenceDate($event->getReferenceDate());

        foreach ($capsules as $capsule) {
            event(new RequestCapsuleSave($capsule));
        }
    }
}
