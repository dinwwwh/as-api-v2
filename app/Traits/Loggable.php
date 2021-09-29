<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Loggable
{

    /**
     * Auto delete related log when a model is deleted
     *
     */
    protected static function bootLoggable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->logs->each(fn (Log $log) => $log->delete());
            }
        });
    }

    /**
     * Write log to database
     *
     */
    public function log(string $message, string $type = 'info', ?array $hiddenData = null): Log
    {
        return $this->logs()->create([
            'message' => $message,
            'type' => $type,
            'hidden_data' => $hiddenData
        ]);
    }

    /**
     * Get logs of model
     *
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
