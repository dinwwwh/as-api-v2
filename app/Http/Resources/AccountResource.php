<?php

namespace App\Http\Resources;

use App\Traits\WithLoad;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'creator' => new UserResource($this->whenLoaded('creator')),
            'updater' => new UserResource($this->whenLoaded('updater')),

            'accountType' => new AccountTypeResource($this->whenLoaded('accountType')),

            'images' => FileResource::collection($this->whenLoaded('images')),
            'mainImage' => new FileResource($this->whenLoaded('mainImage')),

            'tags' => TagResource::collection($this->whenLoaded('tags')),

            $this->mergeWhen(
                auth()->check() && request('_sensitive'),
                fn () => [
                    'cost' => auth()->user()->can('readCost', $this->resource) ? $this->resource->cost : null,
                ],
            ),

            $this->mergeWhen(
                auth()->check() && request('_abilities'),
                fn () => [
                    'canUpdate' => auth()->user()->can('update', $this->resource),
                ],
            ),
        ]);
    }
}
