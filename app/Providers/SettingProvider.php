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
            foreach (Setting::all() as $setting) {
                if ($setting->assigned_config_key) {
                    config([$setting->assigned_config_key => $setting->value]);
                }
                config(['settings.' . $setting->key => $setting->value]);
            }
        });
    }
}
