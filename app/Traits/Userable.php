<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Userable
{
    /**
     * Auto delete relationships when model delete permanently
     *
     */
    protected static function bootUserable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->users()->sync([]);
            }
        });
    }

    /**
     * Get users of model
     *
     */
    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'userable');
    }

    /**
     * Determine whether model has a user
     *
     */
    public function hasUser(User|int $user): bool
    {
        $userId = is_int($user) ? $user : $user->getKey();
        return !is_null($this->users()->where('id', $userId)->get());
    }
}
