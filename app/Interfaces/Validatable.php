<?php

namespace App\Interfaces;

use App\Models\Validation;
use App\Models\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;

interface Validatable
{
    /**
     * Validate the model
     *
     */
    public function validate(?int $type = null): ?Validation;

    /**
     * Get readable values of `readable_fields` in validation
     * Will call when approver start approving
     * return need follow structure [
     *    $readable_field => $value,
     *    ...
     * ]
     *
     */
    public function getReadableValues(Validation $validation): array;

    /**
     * Handle updatable values of `updatable_fields` in validation
     * Will call when approver end approving
     * $values need follow structure [
     *    $updatable_field => $value,
     *    ...
     * ]
     */
    public function handleUpdatableValues(Validation $validation): void;

    /**
     * Relationship to Validation model
     *
     */
    public function validations(): Relation;
}
