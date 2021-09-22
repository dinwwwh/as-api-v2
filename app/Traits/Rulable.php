<?php

namespace App\Traits;

use App\Models\Rule;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Rulable
{
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
