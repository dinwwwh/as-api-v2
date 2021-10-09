<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;
use Str;

class Tag extends Model
{
    use HasFactory, CreatorAndUpdater, Searchable;

    public const CATEGORY_TYPE = 1;
    public const PROPERTY_TYPE = null;

    protected  $primaryKey = 'slug';
    protected  $keyType = 'string';
    public  $incrementing = false;

    protected  $touches = [];
    protected  $fillable = ['slug', 'name', 'description', 'type', 'parent_slug'];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Get parent tag of this tag.
     * Parent tag is tag that it describe totally child tag.
     *
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_slug');
    }

    /**
     * Get parent tag of this tag.
     * Parent tag is tag that it describe totally child tag.
     *
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_slug');
    }

    public function accounts(): MorphToMany
    {
        return $this->morphedByMany(Account::class, 'taggable');
    }

    /**
     * Like first or create but for many tags concurrently
     *
     */
    public static function firstOrCreateMany(array $tags, ?int $type = null): Collection
    {
        $result = new Collection();
        foreach ($tags as $tag) {
            $tagModel =  static::firstOrCreate(['slug' => Str::slug($tag['name'])], [
                'name' => $tag['name'],
                'description' => $tag['description'] ?? null,
                'type' => $type,
            ])->getRepresentation();

            if ($tagModel) $result->push($tagModel);
        }

        return $result;
    }

    /**
     * Get outer-parent of this model
     *
     */
    public function getRepresentation(): static|null
    {
        $tag = clone $this;

        for ($i = 0; !is_null($tag->parent_slug); $i++) {
            if ($i > 12) return null;
            $tag = $tag->parent;
        }

        return $tag;
    }
}
