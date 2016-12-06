<?php

namespace App\Events;

use App\Http\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ShippingStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    protected $user_id;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['test-channel'];
//        return new PrivateChannel('App.User.'.$this->user->id);
    }
}
