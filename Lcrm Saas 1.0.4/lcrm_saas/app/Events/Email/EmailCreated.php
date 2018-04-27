<?php

namespace App\Events\Email;

use App\Events\Event;
use App\Models\Email;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Email
     */
    public $email;

    /**
     * Create a new event instance.
     *
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['lcrm_channel.user_' . $this->email->receiver->id];
    }
}
