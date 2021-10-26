<?php

namespace App\Models;

use App\Interfaces\Validatable;
use App\Models\Pivot\AccountAccountInfo;
use App\Traits\CreatorAndUpdater;
use App\Traits\Filable;
use App\Traits\Loggable;
use App\Traits\Taggable;
use App\Traits\Validationable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Account extends Model implements Validatable
{
    use HasFactory,
        CreatorAndUpdater,
        Loggable,
        Taggable,
        Searchable,
        Filable,
        Validationable;

    public const CHECKING_STATUS = 1;
    public const SELLING_STATUS = 2;
    public const BOUGHT_STATUS = 3;
    public const ERROR_STATUS = 4;

    protected  $touches = [];
    protected  $fillable = [
        'description',
        'tax',
        'cost',
        'price',
        'account_type_id',
        'bought_at_price',
        'bought_at',
        'buyer_id',
        'confirmed_at',
        'paid_at',
        'status' // computed property
    ];
    protected  $hidden = ['cost'];
    protected  $casts = [
        'bought_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime'
    ];
    protected  $with = ['accountType'];
    protected  $withCount = [];

    protected static function booted()
    {
        static::creating(function (self $account) {
            $account->status = $account->getSyncedStatus();
        });

        static::updating(function (self $account) {
            $account->status = $account->getSyncedStatus();
        });
    }

    /**
     * Get synced status
     *
     */
    public function getSyncedStatus()
    {
        if (
            $this->validations()
            ->where(function (Builder $builder) {
                return $builder->where('is_error', true);
            })
            ->first()
        )
            return static::ERROR_STATUS;

        if (
            $this->validations()
            ->where(function (Builder $builder) {
                return $builder->where('is_approving', true)
                    ->orWhereNull('approver_id');
            })
            ->first()
        )
            return static::CHECKING_STATUS;

        if ($this->bought_at)
            return static::BOUGHT_STATUS;

        return static::SELLING_STATUS;
    }

    /**
     * Determine whether this account is selling
     *
     */
    public function isSelling(): bool
    {
        return $this->status == static::SELLING_STATUS;
    }

    /**
     * Determine whether this account is error
     *
     */
    public function isError(): bool
    {
        return $this->status == static::ERROR_STATUS;
    }

    /**
     * Determine whether this account is bought
     * and pending confirming
     *
     */
    public function isBought(): bool
    {
        return $this->status == static::BOUGHT_STATUS
            && $this->confirmed_at
            && $this->confirmed_at->gt(now());
    }

    /**
     * Determine whether this account is bought
     * and confirmed oke
     *
     */
    public function isBoughtOke(): bool
    {
        return $this->status == static::BOUGHT_STATUS
            && $this->confirmed_at
            && $this->confirmed_at->lte(now());
    }

    /**
     * Get data use for search `laravel-scout`
     *
     */
    public function toSearchableArray(): array
    {
        $this->loadMissing('tags');
        return $this->toArray();
    }

    /**
     * Get account type that this model belong to
     *
     */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get account infos of this model
     *
     */
    public function infos(): BelongsToMany
    {
        return $this->belongsToMany(AccountInfo::class)
            ->withPivot(['value'])
            ->using(AccountAccountInfo::class)
            ->withTimestamps();
    }

    /**
     * Get account infos of this model
     * That creator can read and update
     * When account selling
     *
     */
    public function creatorInfos(): BelongsToMany
    {
        return $this->belongsToMany(AccountInfo::class)
            ->where('can_creator', true)
            ->withPivot(['value'])
            ->using(AccountAccountInfo::class)
            ->withTimestamps();
    }

    /**
     * Get account infos of this model
     * That buyer can read
     * When account bought and pending confirming
     *
     */
    public function buyerInfos(): BelongsToMany
    {
        return $this->belongsToMany(AccountInfo::class)
            ->where('can_buyer', true)
            ->withPivot(['value'])
            ->using(AccountAccountInfo::class)
            ->withTimestamps();
    }

    /**
     * Get account infos of this model
     * That buyer can read
     * When account bought and confirmed account oke
     *
     */
    public function buyerOkeInfos(): BelongsToMany
    {
        return $this->belongsToMany(AccountInfo::class)
            ->where('can_buyer_oke', true)
            ->withPivot(['value'])
            ->using(AccountAccountInfo::class)
            ->withTimestamps();
    }

    /**
     * Get main image - image has minimum order
     *
     */
    public function mainImage()
    {
        return $this->morphOne(File::class, 'filable')
            ->where(function (Builder $builder) {
                foreach (File::IMAGE_EXTENSIONS as $key => $ex) {
                    if (array_key_first(File::IMAGE_EXTENSIONS) == $key) {
                        $builder->where('path', 'like', "%.${ex}");
                    } else {
                        $builder->orWhere('path', 'like', "%.${ex}");
                    }
                }
            })
            ->orderBy('order');
    }

    /**
     * Get buyer of this account
     *
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Validate this model
     *
     */
    public function validate(?int $type = null): ?Validation
    {
        $validatorable =  $this->accountType
            ->validatorables()
            ->where('type', $type)
            ->first();

        if ($validatorable) {
            $this->status = static::CHECKING_STATUS;

            return $this->validations()->create([
                'validatorable_id' => $validatorable->getKey(),
            ]);
        }

        return  null;
    }

    /**
     * Get readable values of `readable_fields` in validation
     * Will call when approver start approving
     * return need follow structure [
     *    $readable_field => $value,
     *    ...
     * ]
     *
     */
    public function getReadableValues(Validation $validation): array
    {
        $accountInfoIds = $validation
            ->validatorable
            ->mapped_readable_fields;

        return $this->infos()
            ->whereIn('id', $accountInfoIds)
            ->get()
            ->mapWithKeys(function (AccountInfo $info) use ($accountInfoIds) {
                foreach ($accountInfoIds as $fieldName => $id) {
                    if (
                        $info->getKey() == $id
                    )
                        return [$fieldName => $info->pivot->value];
                }
            })
            ->toArray();
    }

    /**
     * Handle updatable values of `updatable_fields` in validation
     * Will call when approver end approving
     * $values need follow structure [
     *    $updatable_field => $value,
     *    ...
     * ]
     */
    public function handleUpdatableValues(Validation $validation): void
    {
        $values = $validation->updated_values;

        if (!is_array($values))
            throw new Exception(
                'Field `updated_values` of a `Validation` instance must be an array,
                when call `handleUpdatableValues` method in `Validatable` instance.'
            );

        $accountInfoIds = $validation
            ->validatorable
            ->mapped_updatable_fields;

        // count how many infos will updated
        $count = 0;

        $this->infos()
            ->whereIn('id', $accountInfoIds)
            ->get()
            ->each(function (AccountInfo $info) use ($values, $accountInfoIds, &$count) {
                foreach ($values as $fieldName => $value) {
                    foreach ($accountInfoIds as $fieldName2 => $id) {
                        if (
                            $fieldName == $fieldName2
                            && $info->getKey() == $id
                        ) {
                            $info->pivot->update([
                                'value' => $value,
                            ]);

                            $count++;
                        }
                    }
                }
            });

        $expectedCount = count($values);
        if ($count != $expectedCount) {
            throw new Exception(
                'Expect updated ' .
                    $expectedCount .
                    ' account infos but updated ' .
                    $count . ' infos'
            );
        }
    }
}
