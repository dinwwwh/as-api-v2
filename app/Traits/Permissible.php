<?php

namespace App\Traits;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Permissible
{
    /**
     * Auto delete relationships when model delete permanently
     *
     */
    protected static function bootPermissible(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->permissions()->sync([]);
            }
        });
    }

    /**
     * Get users of model
     *
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(Permission::class, 'permissible');
    }

    /**
     * Determine whether model has a permission
     *
     */
    public function hasPermission(Permission|string $permission): bool
    {
        $permissionKey = is_string($permission) ? $permission : $permission->getKey();
        return !is_null($this->permissions()->where('key', $permissionKey)->first(['key']));
    }

    /**
     * Determine whether model has any permissions
     *
     */
    public function hasAnyPermissions(Collection|array $permissions): bool
    {
        $permissionKeys = is_array($permissions) ? $permissions : $permissions->pluck('key')->toArray();
        return !is_null($this->permissions()->whereIn('key', $permissionKeys)->first(['key']));
    }

    /**
     * Determine whether model has all permissions
     *
     */
    public function hasAllPermissions(Collection|array $permissions): bool
    {
        $permissionKeys = is_array($permissions) ? $permissions : $permissions->pluck('key')->toArray();
        return  count($permissionKeys) == $this->permissions()->whereIn('key', $permissionKeys)->count();
    }
}
