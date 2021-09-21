<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Loggable
{
    /**
     * Write log to database
     *
     */
    public function log(string $message, string $type = 'info', ?array $hiddenData = null)
    {
        $this->logs()->create([
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
