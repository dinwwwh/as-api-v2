<?php

namespace App\Providers;

use Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(!app()->isProduction());

        // Helper to convert keys of array to 'camelCase'
        Arr::macro('camel', function (array $arr): array {
            $camelArr = [];
            foreach ($arr as $key => $value) {
                $camelArr[Str::camel($key)] = $value;
            }
            return $camelArr;
        });

        // Helper to convert keys of array to 'snack_case'
        Arr::macro('snake', function (array $arr): array {
            $camelArr = [];
            foreach ($arr as $key => $value) {
                $camelArr[Str::snake($key)] = $value;
            }
            return $camelArr;
        });
    }
}
