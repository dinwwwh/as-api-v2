<?php

namespace App\Traits;

use App\Models\Validatorable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ValidatorableParent
{
    /**
     * Auto delete relationships when model delete permanently
     *
     */
    protected static function bootValidatorableParent(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->validatorables->each(fn (Validatorable $validatorable) => $validatorable->delete());
            }
        });
    }

    /**
     * Get validators of the model
     *
     */
    public function validatorables(): MorphMany
    {
        return $this->morphMany(Validatorable::class, 'parent')
            ->orderBy('order');
    }
}
