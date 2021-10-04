<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Filable
{

    /**
     * Auto delete related log when a model is deleted
     *
     */
    protected static function bootFilable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->files->each(fn (File $file) => $file->delete());
            }
        });
    }

    /**
     * Get files of model
     *
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'filable')
            ->orderBy('order');
    }

    /**
     * Get images of model
     *
     */
    public function images(): MorphMany
    {
        return $this->morphMany(File::class, 'filable')
            ->when(File::IMAGE_EXTENSIONS, function (Builder $builder) {
                foreach (File::IMAGE_EXTENSIONS as $key => $ex) {
                    if (array_key_first(File::IMAGE_EXTENSIONS) == $key) {
                        $builder->where('path', 'like', "%.${ex}");
                    } else {
                        $builder->orWhere('path', 'like', "%.${ex}");
                    }
                }
            })
            ->orderBy('order');
    }
}
