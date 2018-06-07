<?php

namespace Equinox\Listeners;

use Equinox\Events\RequestDataModify;
use Equinox\Services\Data\DataModifyService;
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
     * The DataModify Service
     * @var DataModifyService
     */
    protected $dataModifyService;

    /**
     * Create the event listener.
     * @param DataModifyService $dataModifyService
     */
    public function __construct(
        DataModifyService $dataModifyService
    ) {
        $this->dataModifyService = $dataModifyService;
    }

    /**
     * Handle the event.
     *
     * @param  RequestDataModify $event
     * @return void
     */
    public function handle(RequestDataModify $event)
    {
        try {
            $this->dataModifyService->modifyRecords($event->getMapping());
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }
    }
}
