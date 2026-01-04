<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CheckProjectSubmissions;
use Illuminate\Support\Facades\Blade;

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

        /*

        Blade::directive('warning', function ($field) {
            return "<?php if(\$warnings && \$warnings->has($field)): ?>
                        <?php foreach(\$warnings->get($field) as \$message): ?>
                            <p class=\"mt-1 text-sm text-amber-600\"><?php echo e(\$message); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>";
        });

        Blade::directive('endwarning', function () {
            return '';
        });
        */


         Blade::directive('warning', function ($field) {
        return "<?php if(!empty(\$warnings[$field] ?? [])): ?>
                    <?php foreach(\$warnings[$field] as \$message): ?>
                        <p class=\"mt-1 text-sm text-amber-600\"><?php echo e(\$message); ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>";

        /**
         * Usage 
         * @warning('name')
         */
    });


    }
}
