<?php

namespace App\Traits;

use Arr;
use Laravel\Scout\Searchable as ScoutSearchable;

/**
 * Need define $searchableRelations in model if you want to search in relations
 *
 */
trait Searchable
{
    use ScoutSearchable;

    /**
     * Auto-load the relations and tag the null fields
     *
     */
    public function toSearchableArray(): array
    {
        $result = $this->load($this?->searchableRelations ?? [])
            ->toArray();

        // Tag the null fields
        $result['_tags'] = array_map(function ($key) {
            return $key . '_is_null';
        }, array_keys(Arr::whereNull($result)));

        // Applies Scout Extended default transformations:
        return $this->transform($result);
    }
}
