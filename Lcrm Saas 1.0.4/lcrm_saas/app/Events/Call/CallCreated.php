<?php

namespace App\Events\Call;

use App\Events\Event;
use App\Models\Call;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CallCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Call
     */
    public $call;

    /**
     * Create a new event instance.
     * @param Call $call
     */
    public function __construct(Call $call)
    {
        $this->call = $call;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['lcrm_channel.user_' . $this->call->responsible->id];
    }
}
