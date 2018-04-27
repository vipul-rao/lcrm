<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\SubscriptionCreated;
use App\Repositories\OrganizationRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;

class SubscriptionCreatedListener
{
    private $userRepository;
    private $settingsRepository;
    private $subscriptionRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        SettingsRepository $settingsRepository,
        SubscriptionRepository $subscriptionRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->settingsRepository = $settingsRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Handle the event.
     *
     * @param  SubscriptionCreated  $event
     * @return void
     */
    public function handle(SubscriptionCreated $event)
    {
        $organization = $this->userRepository->getOrganization();
        $subscription = $this->subscriptionRepository->find($event->subscription);

        $settings = $this->settingsRepository->getAll();
        $site_email = isset($settings['site_email']) ? $settings['site_email'] : "";

        Mail::to($organization->email)->send(new \App\Mail\SubscriptionCreated([
            'from' => $site_email,
            'subject' => trans('emails.thank_you_for_subscribing_to_our_plan'),
            'subscription' => $subscription,
        ]));
    }
}
