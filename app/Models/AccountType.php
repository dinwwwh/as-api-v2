<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use App\Traits\Loggable;
use App\Traits\Taggable;
use App\Traits\Userable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    use HasFactory, CreatorAndUpdater, Taggable, Userable, Loggable;

    protected  $touches = [];
    protected  $fillable = [
        'name',
        'description',
    ];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Get accounts of this model
     *
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get account infos of this model
     *
     */
    public function accountInfos(): HasMany
    {
        return $this->hasMany(AccountInfo::class);
    }

    /**
     * Get account infos that creator can read and update of this model
     * When account selling
     *
     */
    public function creatorAccountInfos(): HasMany
    {
        return $this->hasMany(AccountInfo::class)
            ->where('can_creator', true);
    }

    /**
     * Get account infos that buyer can read of this model
     *  When account bought and pending confirming
     */
    public function buyerAccountInfos(): HasMany
    {
        return $this->hasMany(AccountInfo::class)
            ->where('can_buyer', true);
    }

    /**
     * Get account infos that buyer can read of this model
     *  When account bought buy buyer :)) and confirmed account oke
     */
    public function buyerOkeAccountInfos(): HasMany
    {
        return $this->hasMany(AccountInfo::class)
            ->where('can_buyer_oke', true);
    }
}
