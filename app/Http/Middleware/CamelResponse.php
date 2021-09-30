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
            $data = json_decode($response->content(), true);
            if (is_array($data)) {
                $response->setData(
                    Arr::camel(
                        $data,
                        -1, //Drawback If in data has un camel case it can't differentiate them
                    )
                );
            }
        }

        return $response;
    }
}
