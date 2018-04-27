<?php namespace App\Listeners;


use App\Events\Call\CallCreated;
use App\Events\Meeting\MeetingCreated;
use App\Events\NotificationEvent;
use App\Models\Call;
use App\Models\User;

class NotificationListener
{
    public function onCallCreated(CallCreated $event)
    {
        $call = $event->call;

        //Get the responsible User
        $user = User::find($call->responsible->id);

        //Store the notification for responsible user.
        $notification = $user->notifications()->create([
            'title'   => 'New call added',
            'type'    => 'call',
            'type_id' => $call->id,
        ]);

        event(new NotificationEvent($notification));
    }

    public function onMeetingCreated(MeetingCreated $event)
    {
        $meeting = $event->meeting;

        //Get the responsible User
        $user = User::find($meeting->responsible->id);

        //Store the notification for responsible user.
        $notification = $user->notifications()->create([
            'title'   => 'New meeting added',
            'type'    => 'meeting',
            'type_id' => $meeting->id,
        ]);

        event(new NotificationEvent($notification));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            MeetingCreated::class,
            'App\Listeners\NotificationListener@onMeetingCreated'
        );

        $events->listen(
            CallCreated::class,
            'App\Listeners\NotificationListener@onCallCreated'
        );

    }
}