<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\CancelSubscription;
use App\Repositories\OrganizationRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\Facades\Mail;

class CancelSubscriptionListener
{
    private $organizationRepository;
    private $subscriptionRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        OrganizationRepository $organizationRepository,
        SubscriptionRepository $subscriptionRepository
    )
    {
        $this->organizationRepository = $organizationRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Handle the event.
     *
     * @param  CancelSubscription  $event
     * @return void
     */
    public function handle(CancelSubscription $event)
    {
        $subscription = $this->subscriptionRepository->find($event->subscription);
        $organization = $this->organizationRepository->find($subscription->organization_id);

        $site_email = config('settings.site_email');

        Mail::to($organization->email)->send(new \App\Mail\CancelSubscription([
            'from' => $site_email,
            'subject' => trans('emails.subscription_canceled'),
            'subscription' => $subscription,
        ]));

        $app_id = config('broadcasting.connections.pusher.app_id');
        $app_key = config('broadcasting.connections.pusher.key');
        $app_secret = config('broadcasting.connections.pusher.secret');
        $app_cluster = 'ap2';

        require base_path().'/vendor/autoload.php';
        $pusher = new \Pusher( $app_key, $app_secret, $app_id, array('cluster' => $app_cluster) );

        $data['message'] = trans('emails.your_subscription_with_plan') .' '. $subscription->name .' '. trans('emails.has_been_canceled')
            .'. '.trans('emails.continue_to_use_the_service_untill').' '. $subscription->ends_at;
        $pusher->trigger('subscription_canceled_channel'.$organization->user_id, 'subscription_canceled_event'.$organization->user_id, $data);
    }
}
