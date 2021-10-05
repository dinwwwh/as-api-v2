<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use App\Traits\Filable;
use App\Traits\Loggable;
use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Account extends Model
{
    use HasFactory,
        CreatorAndUpdater,
        Loggable,
        Taggable,
        Searchable,
        Filable;

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
    ];
    protected  $hidden = ['cost'];
    protected  $casts = [
        'bought_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime'
    ];
    protected  $with = ['accountType'];
    protected  $withCount = [];

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
