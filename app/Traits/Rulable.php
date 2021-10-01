<?php

namespace App\Traits;

use App\Models\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Rulable
{
    /**
     * Auto delete relationships when model delete permanently
     *
     */
    protected static function bootRulable(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') ? $model->isForceDeleting() : true) {
                $model->rules()->sync([]);
            }
        });
    }

    /**
     * Handle sync rule relationships easily
     *
     */
    public function rule(array $rules)
    {
        return $this->rules()->sync(array_map(fn ($rule) => $rule['key'], $rules));
    }

    /**
     * Convert all rules of this model to laravel rules used in validation
     *
     */
    public function getValidatableRules(): array
    {
        return $this->rules->pluck('key')->toArray();
    }

    /**
     * Get all rule of this model
     *
     */
    public function rules(): MorphToMany
    {
        return $this->morphToMany(Rule::class, 'rulable')
            ->withTimestamps();
    }
}
