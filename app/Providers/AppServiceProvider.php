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
        Arr::macro('camel', function (array $arr, int $depth = 1): array {
            if ($depth == 0) return $arr;
            $camelArr = [];
            foreach ($arr as $key => $value) {
                $camelArr[Str::camel($key)] = is_array($value)
                    ? static::camel($value, $depth - 1)
                    : $value;
            }
            return $camelArr;
        });

        // Helper to convert keys of array to 'snack_case'
        Arr::macro('snake', function (array $arr, int $depth = 1): array {
            if ($depth == 0) return $arr;
            $snakeArr = [];
            foreach ($arr as $key => $value) {
                $snakeArr[Str::snake($key)] = is_array($value)
                    ? static::snake($value, $depth - 1)
                    : $value;
            }
            return $snakeArr;
        });
    }
}
