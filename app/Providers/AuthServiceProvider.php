<?php

namespace App\Providers;

use Auth;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Custom reset password url when user forgot
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return env('APP_BASE_RESET_PASSWORD_URL', env('APP_URL') . '/reset-password') . '?token=' . $token;
        });

        // Determine whether current auth has a given permission
        Auth::macro('hasPermission', function (string $permissionKey): bool {
            if (!$this->check()) return false;
            if (
                $this->user()->permissions()->where('key', $permissionKey)->first(['key'])
            ) return true;
            if (
                $this->user()->roles()->whereRelation('permissions', 'key', $permissionKey)->first(['key'])
            ) return true;

            return false;
        });

        // Determine whether current auth has all permissions
        Auth::macro('hasAllPermissions', function (string|array $permissionKeys): bool {
            if (!$this->check()) return false;
            foreach ((array)$permissionKeys as $permissionKey) {
                if (!$this->hasPermission($permissionKey)) return false;
            }
            return true;
        });

        // Determine whether current auth has any permissions
        Auth::macro('hasAnyPermissions', function (string|array $permissionKeys): bool {
            if (!$this->check()) return false;
            foreach ((array)$permissionKeys as $permissionKey) {
                if ($this->hasPermission($permissionKey)) return true;
            }
            return false;
        });
    }
}
