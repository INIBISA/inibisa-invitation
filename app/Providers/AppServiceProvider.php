<?php

namespace App\Providers;

use App\Models\Invitation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        Model::preventLazyLoading(! app()->isProduction());

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(Str::lower($request->string('email')->toString()).'|'.$request->ip());
        });

        RateLimiter::for('guest-interaction', function (Request $request) {
            $invitation = $request->route('invitation');
            $invitationKey = $invitation instanceof Invitation ? $invitation->getRouteKey() : 'unknown';

            return Limit::perMinute(5)->by($invitationKey.'|'.$request->ip());
        });

        RateLimiter::for('youtube-search', function (Request $request) {
            return Limit::perMinute(10)->by((string) $request->user()?->getAuthIdentifier());
        });
    }
}
