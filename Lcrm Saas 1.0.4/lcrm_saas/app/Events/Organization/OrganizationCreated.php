<?php

namespace App\Events\Organization;

use App\Models\Organization;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class OrganizationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $organization;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
