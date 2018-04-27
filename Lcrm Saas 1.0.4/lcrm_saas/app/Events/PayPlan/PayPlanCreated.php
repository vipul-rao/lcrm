<?php

namespace App\Events\PayPlan;

use App\Events\Event;
use App\Models\PayPlan;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PayPlanCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var PayPlan
     */
    public $payPlan;

    /**
     * Create a new event instance.
     *
     * @param PayPlan $payPlan
     */
    public function __construct(PayPlan $payPlan)
    {
        $this->payPlan = $payPlan;
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
