<?php

namespace App\Providers;

use App\Services\SeoService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Models\Appointment;
use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Models\Review;
use App\Models\Message;
use App\Models\User;
use App\Observers\AppointmentObserver;
use App\Observers\QuoteObserver;
use App\Observers\QuoteRequestObserver;
use App\Observers\ReviewObserver;
use App\Observers\MessageObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register SeoService as singleton
        $this->app->singleton(SeoService::class, function () {
            return new SeoService();
        });

        // Register alias for easier access
        $this->app->alias(SeoService::class, 'seo');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useTailwind();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Register model observers for webhook events
        Appointment::observe(AppointmentObserver::class);
        Quote::observe(QuoteObserver::class);
        QuoteRequest::observe(QuoteRequestObserver::class);
        Review::observe(ReviewObserver::class);
        Message::observe(MessageObserver::class);
        User::observe(UserObserver::class);
    }
}
