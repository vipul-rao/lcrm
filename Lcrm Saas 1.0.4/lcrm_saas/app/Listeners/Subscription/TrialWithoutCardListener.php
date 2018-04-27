<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\TrialWithoutCard;
use App\Repositories\OrganizationRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\Facades\Mail;

class TrialWithoutCardListener
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
     * @param  TrialWithoutCard  $event
     * @return void
     */
    public function handle(TrialWithoutCard $event)
    {
        $organization = $this->organizationRepository->find($event->organization);

        $settings = $this->settingsRepository->getAll();
        $site_name = isset($settings['site_name']) ? $settings['site_name'] : config('app.name');
        $site_email = isset($settings['site_email']) ? $settings['site_email'] : "";

        Mail::to($organization->email)->send(new \App\Mail\TrialWithoutCard([
            'from' => $site_email,
            'subject' => trans('emails.welcome_to').' '.$site_name,
            'organization' => $organization,
        ]));
    }
}
