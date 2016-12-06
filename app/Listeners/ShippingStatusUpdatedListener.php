<?php

namespace App\Listeners;

use App\Events\ShippingStatusUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShippingStatusUpdatedListener
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
     * @param  ShippingStatusUpdated  $event
     * @return void
     */
    public function handle(ShippingStatusUpdated $event)
    {
        //
    }
}
