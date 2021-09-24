<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RechargedCardResource extends JsonResource
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
                auth()->check() && request('_sensitiveAttributes'),
                fn () => [
                    'code' => auth()->user()->can('readCode', $this->resource) ? $this->code : 0,
                ],
            ),

            $this->mergeWhen(
                auth()->check() && request('_abilities'),
                fn () => [
                    'canStartApproving' => auth()->user()->can('startApproving', $this->resource),
                    'canEndApproving' => auth()->user()->can('endApproving', $this->resource),
                ],
            ),
        ]);
    }
}
