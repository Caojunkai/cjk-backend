<?php

namespace App\Providers;

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
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        'Illuminate\Database\Events\QueryExecuted' => [
            'App\Listeners\QueryListener'
        ],
        'App\Events\LogEvent' => [
            'App\Listeners\LogListener'
        ],
        'App\Events\ShippingStatusUpdated' =>[
              'App\Listeners\ShippingStatusUpdatedListener',
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
