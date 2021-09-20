<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CreatorAndUpdater
{
    /**
     * This method is called upon instantiation of the Eloquent Model.
     * Add `updater_id` and `creator_id` to fillable property of model
     *
     * @return
     */
    protected function initializeCreatorAndUpdater(): void
    {
        # Just run when user use fillable property instead of guarded.
        if (!empty($this->fillable)) {
            $this->fillable[] = 'creator_id';
            $this->fillable[] = 'updater_id';
        }
    }

    /**
     * Declare updater and creator to fields of model
     *
     */
    protected static function bootCreatorAndUpdater(): void
    {
        static::creating(function ($model) {
            $model->creator_id = $model->creator_id ?? auth()->user()?->getKey();
        });

        static::updating(function ($model) {
            $model->updater_id = $model->updater_id ?? auth()->user()?->getKey();
        });
    }

    /**
     * Get Creator of the model
     *
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get latest updater of the model
     *
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updater_id');
    }
}
