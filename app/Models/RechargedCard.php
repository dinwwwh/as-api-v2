<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargedCard extends Model
{
    use HasFactory;

    protected  $touches = [];
    protected  $fillable = [
        'serial', 'code', 'telco', 'face_value', 'real_face_value',
        'received_value', 'status', 'description', 'approver_id',
    ];
    protected  $hidden = ['code'];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];
}
