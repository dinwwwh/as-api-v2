<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;

/**
 * Is relationship of `validatorable` model
 * `validationable` relationship of this model
 * must implements `App\Interfaces\Validatable` interface
 *
 */
class Validation extends Model
{
    use HasFactory, CreatorAndUpdater, Searchable;

    protected  $touches = [];
    protected  $fillable = [
        'approver_id',
        'is_error',
        'is_approving',
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
    protected  $with = [];
    protected  $withCount = [];

    /**
     * auto touch to validationable in model layer
     * (it will fire events of validationable)
     * If use $touches it not touch on db layer
     * (not fire any events of validationable)
     *
     * And auto call `handleUpdatableValues` on validationable when needed
     *
     * Need sleep per event relate database, because dispatch super fast
     * => database not updated yet (likely)
     *
     */
    protected static function booted()
    {
        static::created(function (self $validation) {
            if ($validation->isSuccess()) $validation->next();
            $validation->touchValidationable();
            $validation->runValidatorCallback();
        });

        static::updated(function (self $validation) {
            if ($validation->isSuccess()) $validation->next();
            $validation->touchValidationable();
            if (
                $validation->isDirty('updated_values')
                && is_array($validation->updated_values)
            )
                $validation->handleUpdatableValues();
        });

        static::deleted(function (self $validation) {
            $validation->touchValidationable();
        });
    }

    /**
     * Run validator callback
     *
     */
    public function runValidatorCallback(): mixed
    {
        return $this->validatorable->validator->runCallback($this);
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
     * Relation must implements `App\Interfaces\Validatable` interface
     *
     */
    public function validationable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * get user relationship as approver
     *
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Determine whether the validation is error
     *
     */
    public function isError(): bool
    {
        return (bool)$this->is_error;
    }

    /**
     * Determine whether the validation is success
     *
     */
    public function isSuccess(): bool
    {
        return !$this->is_error
            && !$this->is_approving
            && $this->approver_id;
    }

    /**
     * Determine whether the validation is approving
     *
     */
    public function isApproving(): bool
    {
        return $this->is_approving;
    }

    /**
     * Get readable values of validationable
     *
     */
    public function getReadableValues(): array
    {
        return $this->validationable->getReadableValues($this);
    }

    /**
     * Handle updatable values of validationable
     *
     */
    public function handleUpdatableValues()
    {
        return $this->validationable->handleUpdatableValues($this);
    }

    /**
     * Touch validationable
     *
     */
    public function touchValidationable()
    {
        sleep(1);
        $this->validationable->touch();
    }

    /**
     * create next validation orderly
     * Run if need continue validate `validationable`
     */
    public function next(): ?static
    {
        $validationable = $this->validationable;
        $currentValidatorable = $this->validatorable;

        # Get `validatorables` has same `type`, same parent `validatorable`
        $validatorables = $currentValidatorable
            ->parent
            ->validatorables()
            ->where('type', $currentValidatorable->type)
            ->get();

        # Find next `validatorable`
        $nextValidatorable = null;
        $hasCurrentValidatorable = false;
        foreach ($validatorables as $validatorable) {
            if ($validatorable->getKey() == $currentValidatorable->getKey()) {
                $hasCurrentValidatorable = true;
            } elseif ($hasCurrentValidatorable) {
                $nextValidatorable = $validatorable;
                break;
            }
        }

        # `nextValidation` is created in the past
        if (
            static::where('id', '>', $this->getKey())
            ->whereIn('validatorable_id', $validatorables->pluck('id'))
            ->where('validationable_id', $this->validationable_id)
            ->where('validationable_type', $this->validationable_type)
            ->first(['id'])
        ) {
            return null;
        }

        # Create next `validation`
        if ($nextValidatorable) {
            return $validationable->validations()->create([
                'validatorable_id' => $nextValidatorable->getKey(),
            ]);
        }

        return null;
    }
}
