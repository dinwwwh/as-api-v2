<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory,
        Searchable;

    protected  $primaryKey = 'key';
    protected  $keyType = 'string';
    public  $incrementing = false;

    protected  $touches = [];
    protected  $fillable = [
        'key', 'value', 'assigned_config_key', 'structure_description',
        'description', 'public', 'rules',
    ];
    protected  $hidden = ['value'];
    protected  $casts = [
        'value' => 'array',
        'rules' => 'array',
    ];
    protected  $with = [];
    protected  $withCount = [];
}
