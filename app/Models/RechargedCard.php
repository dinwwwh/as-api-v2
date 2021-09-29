<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RechargedCard extends Model
{
    use HasFactory, CreatorAndUpdater;

    protected  $touches = [];
    protected  $fillable = [
        'serial', 'code', 'telco', 'face_value', 'real_face_value',
        'received_value', 'description', 'approver_id', 'paid_at',
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
}
