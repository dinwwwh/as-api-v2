<?php

namespace App\Traits;

/**
 * Request model has `callback` field to use this trait
 * Make a model have ability call own method in programmatically
 *
 */
trait Callbackable
{
    /**
     * Add `callback` to fillable
     * Add `callback` to casts with json
     *
     * @return
     */
    protected function initializeCallbackable(): void
    {
        # Just run when user use fillable property instead of guarded.
        if (!empty($this->fillable)) {
            $this->fillable[] = 'callback';
        }

        $this->casts['callback'] = 'array';
    }

    // run callback of model
    public function runCallback(...$params): mixed
    {
        if (!$this->callback) return null;

        [$class, $method] = $this->callback;
        return $class::$method($this, ...$params);
    }
}
