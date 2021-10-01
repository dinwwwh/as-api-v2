<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use App\Traits\Rulable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountInfo extends Model
{
    use HasFactory, CreatorAndUpdater, Rulable;

    protected  $touches = [];
    protected  $fillable = ['name', 'description', 'account_type_id'];
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
}
