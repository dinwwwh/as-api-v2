<?php

namespace App\Traits;

use App\Models\Pivot\Validatorable as PivotValidatorable;
use App\Models\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Validatorable
{
    /**
     * Auto delete relationships when model delete permanently
     *
     */
    protected static function bootValidatorable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->validators()->sync([]);
            }
        });
    }

    /**
     * Get validators of the model
     *
     */
    public function validators(): MorphToMany
    {
        return $this->morphToMany(Validator::class, 'validatorable')
            ->withPivot(['mapped_readable_fields', 'mapped_updatable_fields'])
            ->using(PivotValidatorable::class)
            ->withTimestamps();
    }
}
