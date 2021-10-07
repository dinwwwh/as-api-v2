<?php

namespace App\Models;

use App\Models\Pivot\AccountAccountInfo;
use App\Traits\CreatorAndUpdater;
use App\Traits\Rulable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccountInfo extends Model
{
    use HasFactory, CreatorAndUpdater, Rulable;

    protected  $touches = ['accountType'];
    protected  $fillable = [
        'name',
        'description',
        'account_type_id',
        'can_creator',
        'can_buyer',
        'can_buyer_oke',
    ];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = ['accountType'];
    protected  $withCount = [];

    /**
     * Get account type that this model belong to
     *
     */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get accounts of this model
     *
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class)
            ->withPivot(['value'])
            ->using(AccountAccountInfo::class)
            ->withTimestamps();
    }
}
