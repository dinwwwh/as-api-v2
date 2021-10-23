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

            'mappedReadableFields' => [
                'present',
                'array',
                'min:' . count($validator->readable_fields),
                'max:' . count($validator->readable_fields),
                'keys:string,' . Rule::in($validator->readable_fields)
            ],
            'mappedReadableFields.*' => [
                'integer',
                Rule::exists('account_infos', 'id')
                    ->where('account_type_id', $accountType->getKey())
            ],

            'mappedUpdatableFields' => [
                'present',
                'array',
                'min:' . count($validator->updatable_fields),
                'max:' . count($validator->updatable_fields),
                'keys:string,' . Rule::in($validator->updatable_fields)
            ],
            'mappedUpdatableFields.*' => [
                'integer',
                Rule::exists('account_infos', 'id')
                    ->where('account_type_id', $accountType->getKey())
            ],
        ];
    }
}
