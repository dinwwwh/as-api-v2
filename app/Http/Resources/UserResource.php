<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

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
                auth()->check() && request('_sensitive'),
                fn () => [
                    'balance' => auth()->user()->can('readBalance', $this->resource) ? $this->balance : null,
                    'email' => auth()->user()->can('readEmail', $this->resource) ? $this->email : null,
                ],
            ),

            $this->mergeWhen(
                request('_computed'),
                fn () => [
                    'avatarUrl' => Storage::urlSmartly($this->avatar_path),
                ],
            ),

            $this->mergeWhen(
                auth()->check() && request('_abilities'),
                fn () => [],
            ),
        ]);
    }
}
