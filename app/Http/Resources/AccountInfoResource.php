<?php

namespace App\Http\Resources;

use App\Http\Resources\Pivot\AccountAccountInfoResource;
use App\Models\Pivot\AccountAccountInfo;
use App\Traits\WithLoad;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountInfoResource extends JsonResource
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

            'pivot' => $this->when($this->pivot, function () {
                switch (true) {
                    case $this->pivot instanceof AccountAccountInfo:
                        return new AccountAccountInfoResource($this->pivot);
                    default:
                        return $this->pivot;
                }
            }),

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
