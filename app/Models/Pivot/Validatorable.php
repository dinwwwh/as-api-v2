<?php

namespace App\Models\Pivot;

use App\Models\AccountType;
use App\Models\Validation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Validatorable extends MorphPivot
{
    protected $table = 'validatorables';
    protected  $hidden = [];
    protected  $casts = [
        'mapped_readable_fields' => 'array',
        'mapped_updatable_fields' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function (self $validatorable) {

            /**
             * When detach an validator relationship
             *  Related [approving or pending] Validations will delete
             *
             */
            if ($validatorable->validatorable_type == (new AccountType)->getMorphClass()) {
                Validation::whereRelation('validationable', 'account_type_id', $validatorable->validatorable_id)
                    ->where('validator_id', $validatorable->validator_id)
                    ->where(
                        fn (Builder $query) =>
                        $query->where('is_approving', true)
                            ->orWhereNull('approver_id')
                    )
                    ->get()
                    ->each(fn (Validation $validation) => $validation->delete());
            }
        });
    }
}
