<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    /**
     * SYNC tags rapidly for model
     *
     */
    public function tag(array $tags, ?int $type = null): array
    {
        return $this->tags()->sync(Tag::firstOrCreateMany($tags, $type));
    }

    /**
     * Get tags of model
     *
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
