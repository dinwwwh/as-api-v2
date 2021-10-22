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
     * Fast sync orderly validator relationship
     *
     */
    public function validator(array $validators): array
    {
        return $this->validators()->sync(
            collect($validators)
                ->mapWithKeys(function ($validator, $order) {
                    return [$validator['id'] => [
                        'mapped_readable_fields' => $validator['pivot']['mappedReadableFields'],
                        'mapped_updatable_fields' => $validator['pivot']['mappedUpdatableFields'],
                        'type' => $validator['pivot']['type'] ?? null,
                        'order' => $order + 1,
                    ]];
                })
                ->toArray(),
        );
    }

    /**
     * Get validators of the model
     *
     */
    public function validators(): MorphToMany
    {
        return $this->morphToMany(Validator::class, 'validatorable')
            ->withPivot(['mapped_readable_fields', 'mapped_updatable_fields', 'type', 'order'])
            ->using(PivotValidatorable::class)
            ->withTimestamps()
            ->orderByPivot('order');
    }


    /**
     * Get validator fees by $type
     *
     */
    public function getValidatorFees(?int $type = null): int
    {
        return $this->validators()
            ->wherePivot('type', $type)
            ->get()
            ->sum('fee');
    }
}
