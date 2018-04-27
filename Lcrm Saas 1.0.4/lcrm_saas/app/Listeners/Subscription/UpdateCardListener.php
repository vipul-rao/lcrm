<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\UpdateCard;
use App\Repositories\OrganizationRepository;
use App\Repositories\SettingsRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class UpdateCardListener
{
    private $organizationRepository;
    private $settingsRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        OrganizationRepository $organizationRepository,
        SettingsRepository $settingsRepository
    )
    {
        $this->organizationRepository = $organizationRepository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Handle the event.
     *
     * @param  UpdateCard  $event
     * @return void
     */
    public function handle(UpdateCard $event)
    {
        $organization = $this->organizationRepository->find($event->organization);

        $settings = $this->settingsRepository->getAll();
        $site_email = isset($settings['site_email']) ? $settings['site_email'] : "";

        Mail::to($organization->email)->send(new \App\Mail\UpdateCard([
            'from' => $site_email,
            'subject' => trans('subscription.update_card'),
            'organization' => $organization,
        ]));
    }
}
