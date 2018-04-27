<?php

namespace App\Listeners\Organization;

use App\Events\Organization\OrganizationCreated;
use App\Mail\UserCreated;
use App\Repositories\SettingsRepository;
use Illuminate\Support\Facades\Mail;

class OrganizationCreatedListener
{
    private $settingsRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        SettingsRepository $settingsRepository
    )
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Handle the event.
     *
     * @param  OrganizationCreated  $event
     * @return void
     */
    public function handle(OrganizationCreated $event)
    {
        $organization = $event->organization;
        $settings = $this->settingsRepository->getAll();
        $site_name = isset($settings['site_name']) ? $settings['site_name'] : config('app.name');
        $site_email = isset($settings['site_email']) ? $settings['site_email'] : "";
        Mail::to($organization->email)->send(new UserCreated([
            'from' => $site_email,
            'subject' => trans('emails.welcome_to').' '.$site_name,
            'name' => $organization->name,
            'site_name' => $site_name,
        ]));
    }
}
