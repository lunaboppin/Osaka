<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('authentik', \SocialiteProviders\Authentik\Provider::class);
        });
        // Share Google Maps API key with all views
        View::share('googleMapsApiKey', config('services.google_maps.key'));

        // Eager-load roles on the authenticated user for nav badge checks
        View::composer('layouts.navigation', function ($view) {
            if ($user = Auth::user()) {
                $user->loadMissing('roles');
            }
        });
    }
}
