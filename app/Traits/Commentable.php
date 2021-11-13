<?php

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Commentable
{
    /**
     * Auto delete related comments when a model is deleted
     *
     */
    protected static function bootCommentable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->comments->each(fn (Comment $comment) => $comment->delete());
            }
        });
    }

    /**
     * Write log to database
     *
     */
    public function comment(string $content): Comment
    {
        return $this->comments()->create([
            'content' => $content,
        ]);
    }

    /**
     * Get comments of model
     *
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
