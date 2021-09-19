<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
                request('_specialAttributes'),
                fn () => [],
            ),

            $this->mergeWhen(
                auth()->check() && request('_abilities'),
                fn () => [],
            ),
        ]);
    }
}
