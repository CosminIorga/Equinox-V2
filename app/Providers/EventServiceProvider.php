<?php

namespace Equinox\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Equinox\Events\RequestCapsuleGenerate' => [
            'Equinox\Listeners\CapsuleGenerate',
        ],
        'Equinox\Events\RequestCapsuleSave' => [
            'Equinox\Listeners\CapsuleSave',
        ],
        'Equinox\Events\RequestDataMap' => [
            'Equinox\Listeners\DataMap',
        ],
        'Equinox\Events\RequestDataModify' => [
            'Equinox\Listeners\DataModify',
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
