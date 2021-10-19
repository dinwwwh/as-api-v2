<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    /**
     * Auto delete relationships when model delete permanently
     *
     */
    protected static function bootTaggable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->tags()->sync([]);
            }
        });
    }

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
