<?php

namespace App\Providers;

use Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;
use Storage;
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

        Storage::macro('urlSmartly', function (string $url): string {
            if (Str::startsWith($url, ['https://', 'http://'])) return $url;
            return config('app.url') . $this->url($url);
        });

        Rule::macro('parse', function (string $rootKey, array $rules, array $extraRootRules = []): array {
            $isExistedRootRules = key_exists('rootRules', $rules);
            $currentIndex = 0;
            $result = [
                $rootKey => $extraRootRules
            ];

            if ($isExistedRootRules) {
                $result[$rootKey] = $rules['rootRules'];
                unset($rules['rootRules']);
            };

            foreach ($rules as $key => $rule) {
                if ($key === $currentIndex && !$isExistedRootRules) {
                    $result[$rootKey][] = $rule;
                    $currentIndex++;
                } elseif (!is_array($rule)) {
                    $result["$rootKey.$key"] = $rule;
                } else {
                    $result  = array_merge(
                        $result,
                        Rule::parse("$rootKey.$key", $rule)
                    );
                }
            }

            return $result;
        });
    }
}
