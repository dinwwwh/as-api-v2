<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory, CreatorAndUpdater;

    protected  $primaryKey = 'key';
    protected  $keyType = 'string';
    public  $incrementing = false;

    protected  $touches = [];
    protected  $fillable = ['key', 'name', 'description', 'color'];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Get permissions that this role has
     *
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Get users that has this role
     *
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
