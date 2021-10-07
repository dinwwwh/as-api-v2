<?php

namespace App\Models\Pivot;

use App\Models\Account;
use App\Models\AccountInfo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AccountAccountInfo extends Pivot
{
    protected  $hidden = ['value'];
    protected  $casts = [];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function accountInfo(): BelongsTo
    {
        return $this->belongsTo(AccountInfo::class);
    }
}
