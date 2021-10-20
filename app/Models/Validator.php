<?php

namespace App\Models;

use App\Traits\Callbackable;
use App\Traits\CreatorAndUpdater;
use App\Traits\Userable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Str;

/**
 * A model has main purpose to describe infos for an `Validation` model
 * Describe who can approve, whe info can read from `validationableModel`
 * ...
 *
 *  `Userable` used to determine who is approver
 */
class Validator extends Model
{
    use HasFactory,
        CreatorAndUpdater,
        Callbackable,
        Searchable,
        Userable;

    protected  $touches = [];
    protected  $fillable = [
        'name',
        'description',
        'approver_description',
        'readable_fields',
        'updatable_fields'
    ];
    protected  $hidden = [];
    protected  $casts = [
        'readable_fields' => 'array',
        'updatable_fields' => 'array',
    ];
    protected  $with = [];
    protected  $withCount = [];

    protected static function booted()
    {
        static::creating(function (self $validator) {
            $validator->slug = Str::slug($validator->name);
        });

        static::updating(function (self $validator) {
            $validator->slug = Str::slug($validator->name);
        });
    }
}
