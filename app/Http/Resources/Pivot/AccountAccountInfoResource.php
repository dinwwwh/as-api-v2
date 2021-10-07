<?php

namespace App\Http\Resources\Pivot;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountAccountInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            $this->mergeWhen(
                auth()->check() && request('_sensitive'),
                fn () => [
                    'value' => auth()->user()->can('readValue', $this->resource) ? $this->value : null,
                ],
            ),
        ]);
    }
}
