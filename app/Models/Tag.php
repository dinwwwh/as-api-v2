<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;

class Tag extends Model
{
    use HasFactory, CreatorAndUpdater;

    protected  $primaryKey = 'slug';
    protected  $keyType = 'string';
    public  $incrementing = false;

    protected  $touches = [];
    protected  $fillable = ['slug', 'name', 'description', 'type'];
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
        return $this->belongsTo(static::class);
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
            ]);

            if (!is_null($tagModel->parent_slug)) {
                $tagModel = $tagModel->parent;
            }

            $result->push($tagModel);
        }

        return $result;
    }
}
