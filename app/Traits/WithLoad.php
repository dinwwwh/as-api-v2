<?php

namespace App\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait WithLoad
{

    /**
     * Auto load _relationships and _counts were required
     *
     */
    public static function withLoad(Collection|Model|Authenticatable|Paginator $resource)
    {
        $resource->load(request('_relationships', []));
        $resource->loadCount(request('_counts', []));

        if ($resource instanceof Collection || $resource instanceof Paginator) {
            return static::collection($resource);
        }

        return new static($resource);
    }
}
