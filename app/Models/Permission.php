<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory, CreatorAndUpdater;

    protected  $primaryKey = 'key';
    protected  $keyType = 'string';
    public  $incrementing = false;

    protected  $touches = [];
    protected  $fillable = ['key', 'name', 'description'];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Get roles that this permission has
     *
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get users that has directly this permission
     *
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
