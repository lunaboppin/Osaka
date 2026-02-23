<?php

namespace App\Providers;

use App\Models\StickerType;
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
        // Share sticker types + current selection with navigation
        View::composer('layouts.navigation', function ($view) {
            if ($user = Auth::user()) {
                $user->loadMissing('roles');
            }

            $view->with('stickerTypes', StickerType::ordered()->get());

            // Resolve current sticker type using centralised logic
            $currentStickerTypeId = StickerType::currentId();

            $view->with('currentStickerType', $currentStickerTypeId
                ? StickerType::find($currentStickerTypeId)
                : null
            );
            $view->with('currentStickerTypeId', $currentStickerTypeId);
        });
    }
}
