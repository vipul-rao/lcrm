<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\User\UserCreated' => [
            'App\Listeners\User\UserCreatedListener',
        ],
        'App\Events\Organization\OrganizationCreated' => [
            'App\Listeners\Organization\OrganizationCreatedListener',
        ],
        'App\Events\Subscription\SubscriptionCreated' => [
            'App\Listeners\Subscription\SubscriptionCreatedListener',
        ],
        'App\Events\Subscription\CancelSubscription' => [
            'App\Listeners\Subscription\CancelSubscriptionListener',
        ],
        'App\Events\Subscription\ResumeSubscription' => [
            'App\Listeners\Subscription\ResumeSubscriptionListener',
        ],
        'App\Events\Subscription\ChangePlan' => [
            'App\Listeners\Subscription\ChangePlanListener',
        ],
        'App\Events\Subscription\UpdateCard' => [
            'App\Listeners\Subscription\UpdateCardListener',
        ],
        'App\Events\Subscription\TrialWithoutCard' => [
            'App\Listeners\Subscription\TrialWithoutCardListener',
        ],
        'App\Events\Subscription\Extend' => [
            'App\Listeners\Subscription\ExtendedListener',
        ],
        'App\Events\Subscription\SuspendSubscription' => [
            'App\Listeners\Subscription\SuspendSubscriptionListener',
        ],
        'App\Events\Subscription\PaypalSubscriptionCreated' => [
            'App\Listeners\Subscription\PaypalSubscriptionCreatedListener',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
