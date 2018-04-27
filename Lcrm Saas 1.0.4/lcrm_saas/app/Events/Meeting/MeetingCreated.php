<?php

namespace App\Events\Meeting;

use App\Events\Event;
use App\Models\Meeting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MeetingCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Meeting
     */
    public $meeting;

    /**
     * Create a new event instance.
     *
     * @param Meeting $meeting
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['lcrm_channel.user_' . $this->meeting->responsible->id];
    }
}
