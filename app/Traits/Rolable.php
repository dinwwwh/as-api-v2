<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Rolable
{
    /**
     * Auto delete relationships when model delete permanently
     *
     */
    protected static function bootRolable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->roles()->sync([]);
            }
        });
    }

    /**
     * Get users of model
     *
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'rolable');
    }

    /**
     * Determine whether model has a role
     *
     */
    public function hasRole(Role|string $role): bool
    {
        $roleKey = is_string($role) ? $role : $role->getKey();
        return !is_null($this->roles()->where('key', $roleKey)->first(['key']));
    }

    /**
     * Determine whether model has any roles
     *
     */
    public function hasAnyRoles(Collection|array $roles): bool
    {
        $roleKeys = is_array($roles) ? $roles : $roles->pluck('key')->toArray();
        return !is_null($this->roles()->whereIn('key', $roleKeys)->first(['key']));
    }

    /**
     * Determine whether model has all roles
     *
     */
    public function hasAllRoles(Collection|array $roles): bool
    {
        $roleKeys = is_array($roles) ? $roles : $roles->pluck('key')->toArray();
        return  count($roleKeys) == $this->roles()->whereIn('key', $roleKeys)->count();
    }
}
