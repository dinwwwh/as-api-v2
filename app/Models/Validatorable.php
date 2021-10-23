<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Validatorable extends Model
{
    use HasFactory, CreatorAndUpdater;

    protected  $hidden = [];
    protected  $touches = [];
    protected  $fillable = [
        'validator_id',
        'parent_id',
        'parent_type',
        'fee',
        'type',
        'order',
        'mapped_readable_fields',
        'mapped_updatable_fields',
    ];
    protected  $with = [];
    protected  $withCount = [];
    protected  $casts = [
        'mapped_readable_fields' => 'array',
        'mapped_updatable_fields' => 'array',
    ];


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

    protected static function booted()
    {
        static::deleted(function (self $validatorable) {

            /**
             * When detach an validator relationship
             *  Related [approving or pending] Validations will delete
             *
             */
            Validation::where('validatorable_id', $validatorable->getKey())
                ->where(function (Builder $query) {
                    return $query
                        ->where('is_approving', true)
                        ->orWhereNull('approver_id');
                })
                ->get()
                ->each(fn (Validation $validation) => $validation->delete());
        });
    }


    /**
     * Get parent relationship
     *
     */
    public function parent(): MorphTo
    {
        return $this->morphTo('parent');
    }

    /**
     * Get validator relationship
     *
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Validator::class);
    }

    /**
     *  Get relationship validation
     *
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }
}
