<?php

namespace App\Http\Resources;

use App\Traits\WithLoad;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class TagResource extends JsonResource
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

            $this->mergeWhen(
                request('_computed'),
                fn () => [
                    'mainImageUrl' => Storage::urlSmartly($this->main_image_path),
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
