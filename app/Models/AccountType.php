<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use App\Traits\Loggable;
use App\Traits\Taggable;
use App\Traits\Userable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccountType extends Model
{
    use HasFactory, CreatorAndUpdater, Taggable, Userable, Loggable;

    protected  $touches = [];
    protected  $fillable = ['name', 'description'];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];
}
