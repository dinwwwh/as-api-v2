<?php

namespace App\Http\Middleware;

use Arr;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CamelResponse
{
    /**
     * Convert response json to camel case.
     *
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $response->setData(
                Arr::camel(
                    json_decode($response->content(), true),
                    2
                )
            );
        }

        return $response;
    }
}
