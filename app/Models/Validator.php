<?php

namespace App\Models;

use App\Traits\Callbackable;
use App\Traits\CreatorAndUpdater;
use App\Traits\Userable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;
use Str;

/**
 * A model has main purpose to describe infos for an `Validation` model
 * Describe who can approve, whe info can read from `validationableModel`
 * ...
 *
 *  `Userable` used to determine who is approver
 */
class Validator extends Model
{
    use HasFactory,
        CreatorAndUpdater,
        Callbackable,
        Searchable,
        Userable;

    /**
     * Types used to describe for `validatorable`
     * WHEN will validate `validatable`
     * ... just describe nonsense if you don't use it
     *
     */
    public const OTHER_TYPE = null;
    public const CREATED_TYPE = 1;
    public const UPDATED_TYPE = 2;
    public const BOUGHT_TYPE = 3;
    public const DAILY_TYPE = 4;
    public const WEEKLY_TYPE = 5;
    public const MONTHLY_TYPE = 6;

    protected  $touches = [];
    protected  $fillable = [
        'name',
        'description',
        'approver_description',
        'readable_fields',
        'updatable_fields',
        'fee',
    ];
    protected  $hidden = [];
    protected  $casts = [
        'readable_fields' => 'array',
        'updatable_fields' => 'array',
    ];
    protected  $with = [];
    protected  $withCount = [];

    protected static function booted()
    {
        static::creating(function (self $validator) {
            $validator->slug = Str::slug($validator->name);
        });

        static::updating(function (self $validator) {
            $validator->slug = Str::slug($validator->name);

            /**
             * To less security problems
             * Specially when remove some fields in list
             * => relations still provide or (ability update) sensitive infos
             * of removed field to approver
             *
             */
            if ($validator->isDirty(['readable_fields', 'updatable_fields'])) {
                $validator->accountTypes()->sync([]);

                Validation::where('validator_id', $validator->getKey())
                    ->where('is_approving', true)
                    ->orWhereNull('approver_id')
                    ->get()
                    ->each(fn (Validation $validation) => $validation->delete());
            }
        });
    }

    /**
     * Get relationship validation
     *
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * Get validatorable account type relationship
     *
     */
    public function accountTypes(): MorphToMany
    {
        return $this->morphedByMany(AccountType::class, 'validatorable');
    }
}
