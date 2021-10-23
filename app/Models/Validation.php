<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;

class Validation extends Model
{
    use HasFactory, CreatorAndUpdater, Searchable;

    protected  $touches = [];
    protected  $fillable = [
        'approver_id',
        'is_error',
        'is_pending',
        'updated_values',
        'description',
        'validationable_id',
        'validationable_type',
        'validatorable_id',
    ];
    protected  $hidden = [
        'updated_values'
    ];
    protected  $casts = [
        'updated_values' => 'array'
    ];
    protected  $with = [
        'validationable'
    ];
    protected  $withCount = [];

    /**
     * auto touch to validationable in model layer
     * (it will fire events of validationable)
     * If use $touches it not touch on db layer
     * (not fire any events of validationable)
     *
     * And auto call `handleUpdatableValues` on validationable when needed
     *
     */
    protected static function booted()
    {
        static::created(function (self $validation) {
            $validation->validatorable->validator->runCallback($validation);
            $validation->validationable->touch();
        });

        static::updated(function (self $validation) {
            $validation->validationable->touch();

            if (
                $validation->isDirty('updated_values')
                && is_array($validation->updated_values)
            )
                $validation->validationable->handleUpdatableValues($validation);
        });

        static::deleted(function (self $validation) {
            $validation->validationable->touch();
        });
    }

    /**
     * Determine whether this model is oldest approvable
     * [can start - can end]
     *
     */
    public function isOldestApprovable(): bool
    {
        # IS APPROVABLE
        if (
            $this->is_approving == false &&
            !is_null($this->approver_id)
        )
            return false;

        # IS OLDEST
        return !!static::where('id', '<', $this->getKey())
            ->where(function (Builder $query) {
                $query->where('is_approving', true)
                    ->orWhereNull('approver_id');
            })
            ->first(['id']);
    }

    /**
     * Get relationship to Validator
     *
     */
    public function validatorable(): BelongsTo
    {
        return $this->belongsTo(Validatorable::class);
    }

    /**
     * Get relationship models
     *
     */
    public function validationable(): MorphTo
    {
        return $this->morphTo();
    }
}
