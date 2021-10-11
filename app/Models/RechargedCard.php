<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RechargedCard extends Model
{
    use HasFactory, CreatorAndUpdater;

    public const THESIEURE_SERVICE = 'thesieure';

    protected  $touches = [];
    protected  $fillable = [
        'serial', 'code', 'telco', 'face_value', 'real_face_value',
        'received_value', 'description', 'approver_id', 'paid_at',
        'service',
    ];
    protected  $hidden = ['code'];
    protected  $casts = [
        'paid_at' => 'datetime'
    ];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Get approver of user
     *
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Determine whether card is approving
     *
     *
     */
    public function isApproving(): bool
    {
        return !is_null($this->approver_id)
            && is_null($this->real_face_value)
            && is_null($this->received_value);
    }
}
