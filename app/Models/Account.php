<?php

namespace App\Models;

use App\Models\Pivot\AccountAccountInfo;
use App\Traits\CreatorAndUpdater;
use App\Traits\Filable;
use App\Traits\Loggable;
use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Account extends Model
{
    use HasFactory,
        CreatorAndUpdater,
        Loggable,
        Taggable,
        Searchable,
        Filable;

    // public const CHECKING_STATUS = 1;
    public const SELLING_STATUS = 2;
    public const BOUGHT_STATUS = 3;
    // public const BOUGHT_OKE_STATUS = 4;
    // public const ERROR_STATUS = 5;

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
            if (is_null($account->bought_at)) {
                $account->status = static::SELLING_STATUS;
            } else {
                $account->status = static::BOUGHT_STATUS;
            }
        });

        static::updating(function (self $account) {
            if (is_null($account->bought_at)) {
                $account->status = static::SELLING_STATUS;
            } else {
                $account->status = static::BOUGHT_STATUS;
            }
        });
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
     * Determine whether this account is bought
     * and pending confirming
     *
     */
    public function isBought(): bool
    {
        return $this->status == static::BOUGHT_STATUS;
    }

    /**
     * Determine whether this account is bought
     * and confirmed oke
     *
     */
    public function isBoughtOke(): bool
    {
        return $this->isBought() && $this->confirmed_at && $this->confirmed_at->lte(now());
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
}
