<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class SettingProvider extends ServiceProvider
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
        rescue(function () {
            $assignedConfigs = Setting::whereNotNull('assigned_config_key')
                ->get()
                ->pluck('value', 'assigned_config_key')
                ->toArray();

            config($assignedConfigs);
        });
    }
}
