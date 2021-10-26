<?php

namespace App\Traits;

use App\Models\Validation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Validationable
{
    /**
     * Auto delete related validations when a model is deleted
     *
     */
    protected static function bootValidationable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->validations->each(fn (Validation $validation) => $validation->delete());
            }
        });
    }

    /**
     * Get relationships with validation
     *
     */
    public function validations(): MorphMany
    {
        return $this->morphMany(Validation::class, 'validationable');
    }
}
