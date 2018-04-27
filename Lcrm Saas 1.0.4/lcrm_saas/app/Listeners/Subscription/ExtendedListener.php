<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\Extend;
use App\Mail\SubscriptionExtended;
use Illuminate\Support\Facades\Mail;

class ExtendedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param Extend $event
     */
    public function handle(Extend $event)
    {
        Mail::to($event->data['organization']->email)
        ->send(new SubscriptionExtended($event->data));

        $app_id = config('broadcasting.connections.pusher.app_id');
        $app_key = config('broadcasting.connections.pusher.key');
        $app_secret = config('broadcasting.connections.pusher.secret');
        $app_cluster = 'ap2';

        require base_path().'/vendor/autoload.php';
        $pusher = new \Pusher( $app_key, $app_secret, $app_id, array('cluster' => $app_cluster) );

        $user_id = $event->data['organization']->user_id;

        $data['message'] = trans('emails.hello').','. trans('emails.your_subscription_for').' '.config('app.name').' '
            .trans('emails.is_extended_by').' '.$event->data['duration'].'Day';
        $pusher->trigger('extend_subscription_channel'.$user_id, 'extend_subscription_event'.$user_id, $data);
        return;
    }
}
