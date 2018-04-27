<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\ChangePlan;
use App\Repositories\OrganizationRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ChangePlanListener
{
    private $organizationRepository;
    private $settingsRepository;
    private $subscriptionRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        OrganizationRepository $organizationRepository,
        SettingsRepository $settingsRepository,
        SubscriptionRepository $subscriptionRepository
    )
    {
        $this->organizationRepository = $organizationRepository;
        $this->settingsRepository = $settingsRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Handle the event.
     *
     * @param  ChangePlan  $event
     * @return void
     */
    public function handle(ChangePlan $event)
    {
        $subscription = $this->subscriptionRepository->find($event->subscription);
        $organization = $this->organizationRepository->find($subscription->organization_id);

        $settings = $this->settingsRepository->getAll();
        $site_email = isset($settings['site_email']) ? $settings['site_email'] : "";

        Mail::to($organization->email)->send(new \App\Mail\ChangePlan([
            'from' => $site_email,
            'subject' => trans('subscription.change_plan'),
            'subscription' => $subscription,
        ]));

        $app_id = config('broadcasting.connections.pusher.app_id');
        $app_key = config('broadcasting.connections.pusher.key');
        $app_secret = config('broadcasting.connections.pusher.secret');
        $app_cluster = 'ap2';

        require base_path().'/vendor/autoload.php';
        $pusher = new \Pusher( $app_key, $app_secret, $app_id, array('cluster' => $app_cluster) );

        $data['message'] = trans('emails.your_plan_is_changed_to') .' '. $subscription->name ;
        $pusher->trigger('change_plan_channel'.$organization->user_id, 'change_plan_event'.$organization->user_id, $data);
    }
}
