<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CheckProjectSubmissions;

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
        Schema::defaultStringLength(191);

        $this->app->booted(function () {
            // app(Schedule::class)->command(CheckProjectSubmissions::class)->everyMinute();
        });


        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

    }
}
