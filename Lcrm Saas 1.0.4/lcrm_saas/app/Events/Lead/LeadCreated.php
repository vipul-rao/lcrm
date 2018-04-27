<?php

namespace App\Events\Lead;

use App\Events\Event;
use App\Models\Lead;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LeadCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Lead
     */
    public $lead;

    /**
     * Create a new event instance.
     *
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
