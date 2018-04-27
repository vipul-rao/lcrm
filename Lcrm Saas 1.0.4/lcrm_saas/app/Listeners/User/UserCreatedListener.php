<?php

namespace App\Listeners\User;

use App\Events\User\UserCreated;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;

class UserCreatedListener
{
    private $userRepository;
    private $settingsRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        SettingsRepository $settingsRepository,
        UserRepository $userRepository
    )
    {
        $this->settingsRepository = $settingsRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $settings = $this->settingsRepository->getAll();
        $site_name = isset($settings['site_name']) ? $settings['site_name'] : config('app.name');
        $site_email = isset($settings['site_email']) ? $settings['site_email'] : "";
        Mail::to($user->email)->send(new \App\Mail\UserCreated([
            'from' => $site_email,
            'subject' => "Welcome to ".$site_name,
            'name' => $user->full_name,
            'site_name' => $site_name,
        ]));
    }
}
