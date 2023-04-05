<?php

namespace App\Providers;

use App\Models\Setting;
use Artisan;
use Illuminate\Support\ServiceProvider;
use Octane;

class SwooleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Auto run schedule:run artisan command every minute
        // Octane::tick('schedule-run', function () {
        //     Artisan::call('schedule:run');
        // })->seconds(60);
    }
}
