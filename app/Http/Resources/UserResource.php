<?php

namespace App\Http\Resources;

use App\Models\AccountType;
use App\Models\RechargedCard;
use App\Models\Tag;
use App\Traits\WithLoad;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class UserResource extends JsonResource
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
                fn () => [
                    'canManageRechargedCard' => auth()->user()->can('manage', RechargedCard::class),
                    'canApproveRechargedCard' => auth()->user()->hasPermission('approve_recharged_card'),
                    'canManageAccountType' => auth()->user()->can('manage', AccountType::class),
                    'canCreateAccountType' => auth()->user()->can('create', AccountType::class),
                    'canManageTag' => auth()->user()->can('manage', Tag::class),
                    'canCreateTag' => auth()->user()->can('create', Tag::class),
                ],
            ),
        ]);
    }
}
