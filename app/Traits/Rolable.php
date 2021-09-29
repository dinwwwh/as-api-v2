<?php

namespace App\Traits;

use App\Models\Role;
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
        return !is_null($this->roles()->where('key', $roleKey)->get());
    }
}
