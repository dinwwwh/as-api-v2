<?php

namespace App\Http\Resources;

use App\Traits\WithLoad;
use Illuminate\Http\Resources\Json\JsonResource;

class ValidationResource extends JsonResource
{
    use WithLoad;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [

            'approver' => new UserResource($this->whenLoaded('approver')),

            $this->mergeWhen(
                auth()->check() && request('_sensitive'),
                fn () => [
                    'validationableInfos' => auth()->user()->can('readValidationableInfos', $this->resource)
                        ? $this->validationable->getReadableValues($this->resource)
                        : null,
                ],
            ),

            $this->mergeWhen(
                auth()->check() && request('_abilities'),
                fn () => [
                    'canStartApproving' => auth()->user()->can('startApproving', $this->resource),
                    'canEndApproving' => auth()->user()->can('endApproving', $this->resource),
                    'canReadValidationableInfos' => auth()->user()->can('readValidationableInfos', $this->resource),
                ],
            ),
        ]);
    }
}
