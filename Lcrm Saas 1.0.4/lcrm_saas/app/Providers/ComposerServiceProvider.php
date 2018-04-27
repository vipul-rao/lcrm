<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Helpers\InitialGenerators;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot()
    {
        View::composer([
            'layouts.subscription',
            'profile',
            'account',
            'layouts.frontend._header',
            'frontend.index',
            'frontend.contactus',
            'layouts.frontend._meta',
            ], function ($view) {
                (new InitialGenerators())->generateData();
            });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
    }
}
