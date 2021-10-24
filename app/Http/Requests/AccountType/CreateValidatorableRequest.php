<?php

namespace App\Http\Requests\AccountType;

use App\Models\Validatorable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateValidatorableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare data for validator
     *
     */
    public function prepareForValidation()
    {
        if (is_array($this->mappedReadableFields))
            $this->merge([
                'mappedReadableFieldKeys' => array_keys($this->mappedReadableFields),
            ]);

        if (is_array($this->mappedUpdatableFields))
            $this->merge([
                'mappedUpdatableFieldKeys' => array_keys($this->mappedUpdatableFields),
            ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $accountType = $this->route('accountType');
        $validator = $this->route('validator');

        return [
            'order' => ['integer'],

            'type' => ['present', Rule::in([
                Validatorable::CREATED_TYPE,
                Validatorable::UPDATED_TYPE,
                Validatorable::BOUGHT_TYPE,
            ])],

            'mappedReadableFieldKeys.*' => ['string', 'distinct', Rule::in($validator->readable_fields)],
            'mappedReadableFields' => [
                'present',
                'array',
                'min:' . count($validator->readable_fields),
                'max:' . count($validator->readable_fields),
            ],
            'mappedReadableFields.*' => [
                'integer',
                Rule::exists('account_infos', 'id')
                    ->where('account_type_id', $accountType->getKey())
            ],

            'mappedUpdatableFieldKeys.*' => ['string', 'distinct', Rule::in($validator->updatable_fields)],
            'mappedUpdatableFields' => [
                'present',
                'array',
                'min:' . count($validator->updatable_fields),
                'max:' . count($validator->updatable_fields),
            ],
            'mappedUpdatableFields.*' => [
                'integer',
                Rule::exists('account_infos', 'id')
                    ->where('account_type_id', $accountType->getKey())
            ],
        ];
    }
}
