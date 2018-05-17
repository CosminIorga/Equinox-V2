<?php

namespace Equinox\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Equinox\Events\RequestCapsuleGeneration' => [
            'Equinox\Listeners\GenerateCapsule',
        ],
        'Equinox\Events\RequestCapsuleSave' => [
            'Equinox\Listeners\SaveCapsule',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
