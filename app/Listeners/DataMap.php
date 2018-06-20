<?php

namespace Equinox\Listeners;

use Equinox\Events\RequestDataMap;
use Equinox\Events\RequestDataModify;
use Equinox\Services\Repositories\DataMapService;
use Illuminate\Contracts\Queue\ShouldQueue;

class DataMap implements ShouldQueue
{

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'data:map';

    /**
     * The Data Service
     * @var DataMapService
     */
    protected $dataMapService;

    /**
     * Create the event listener.
     * @param DataMapService $dataMapService
     */
    public function __construct(
        DataMapService $dataMapService
    ) {
        $this->dataMapService = $dataMapService;
    }

    /**
     * Handle the event.
     *
     * @param  RequestDataMap $event
     * @return void
     */
    public function handle(RequestDataMap $event)
    {
        try {
            $mapping = $this->dataMapService->groupRecordsByDefinedCapsules($event->getData());

            foreach ($mapping as $mapData) {
                event(new RequestDataModify($mapData));
            }
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }
    }
}
