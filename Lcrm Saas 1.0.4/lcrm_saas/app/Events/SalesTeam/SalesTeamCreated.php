<?php

namespace App\Events\SalesTeam;

use App\Events\Event;
use App\Models\Salesteam;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SalesTeamCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Salesteam
     */
    public $salesteam;

    /**
     * Create a new event instance.
     * @param Salesteam $salesteam
     */
    public function __construct(Salesteam $salesteam)
    {
        $this->salesteam = $salesteam;
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
