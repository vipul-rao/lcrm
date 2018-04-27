<?php

namespace App\Events\Opportunity;

use App\Events\Event;
use App\Models\Opportunity;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OpportunityCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Opportunity
     */
    public $opportunity;

    /**
     * Create a new event instance.
     *
     * @param Opportunity $opportunity
     */
    public function __construct(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
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
