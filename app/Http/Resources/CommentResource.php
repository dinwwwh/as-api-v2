<?php

namespace App\Http\Resources;

use App\Models\Account;
use App\Traits\WithLoad;
use Illuminate\Http\Resources\Json\JsonResource;
use \Illuminate\Http\Resources\MissingValue;

class CommentResource extends JsonResource
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

            'commentable' => $this->when(
                !$this->whenLoaded('commentable')->isMissing(),
                function () {
                    switch (true) {
                        case $this->commentable instanceof Account:
                            return new AccountResource($this->commentable);
                        default:
                            return $this->commentable;
                    }
                }
            ),
        ]);
    }
}
