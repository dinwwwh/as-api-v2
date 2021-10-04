<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Storage;

class File extends Model
{
    use HasFactory, CreatorAndUpdater;

    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp'];

    protected  $touches = [];
    protected  $fillable = ['path', 'type', 'description', 'order'];
    protected  $hidden = [];
    protected  $casts = [];
    protected  $with = [];
    protected  $withCount = [];

    /**
     * Auto delete related log when a model is deleted
     *
     */
    protected static function booted(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                Storage::delete($model->path);
            }
        });
    }

    /**
     * Get owner of this file
     *
     */
    public function filable(): MorphTo
    {
        return $this->morphTo();
    }
}
