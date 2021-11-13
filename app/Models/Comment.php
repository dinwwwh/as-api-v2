<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;

class Comment extends Model
{
    use HasFactory,
        CreatorAndUpdater,
        Searchable;

    protected  $touches = [];
    protected  $fillable = ['content', 'commentable_type', 'commentable_id'];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Get owner of this model
     *
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
