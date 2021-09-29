<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountTypeResource extends JsonResource
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
            'creator' => new UserResource($this->whenLoaded('creator')),
            'updater' => new UserResource($this->whenLoaded('updater')),

            $this->mergeWhen(
                auth()->check() && request('_abilities'),
                fn () => [
                    'canUpdate' => auth()->user()->can('update', $this->resource),
                    'canDelete' => auth()->user()->can('delete', $this->resource),
                ],
            ),
        ]);
    }
}
