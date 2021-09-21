<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Log extends Model
{
    use HasFactory, CreatorAndUpdater;

    protected  $touches = [];
    protected  $fillable = ['message', 'type', 'hidden_data'];
    protected  $hidden = ['hidden_data'];
    protected  $casts = [
        'hidden_data' => 'array',
    ];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Get owner of this log
     *
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
