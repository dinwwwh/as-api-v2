<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
