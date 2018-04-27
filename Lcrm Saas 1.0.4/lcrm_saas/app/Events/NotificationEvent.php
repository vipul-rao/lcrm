<?php namespace App\Events;


use App\Models\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationEvent extends Event implements ShouldBroadcast
{
    /**
     * @var Notification
     */
    public $notification;

    /**
     * NotificationEvent constructor.
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['lcrm_channel.user_' . $this->notification->user_id];
    }
}