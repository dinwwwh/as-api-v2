<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Validatorable extends Pivot
{
    protected  $hidden = ['value'];
    protected  $casts = [
        'mapped_readable_fields' => 'array',
        'mapped_updatable_fields' => 'array',
    ];
}
