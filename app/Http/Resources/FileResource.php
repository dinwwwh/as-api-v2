<?php

namespace App\Http\Resources;

use App\Traits\WithLoad;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class FileResource extends JsonResource
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

            'filable' => $this->when(
                $this->whenLoaded('filable'),
                function () {
                    switch (true) {
                            // case $this->loggable instanceof ABC:
                            //     return new ABCResource($this->loggable);
                        default:
                            return $this->loggable;
                    }
                }
            ),

            $this->mergeWhen(
                request('_computed'),
                fn () => [
                    'url' => Storage::urlSmartly($this->path),
                ],
            ),
        ]);
    }
}
