<?php

namespace App\Http\Requests\Validation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EndApprovingRequest extends FormRequest
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
     * Prepare data for validation
     *
     */
    public function prepareForValidation()
    {
        if (is_array($this->updatedValues))
            $this->merge([
                'updatedValueKeys' => array_keys($this->updatedValues),
            ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validator = $this->route('validation')->validatorable->validator;

        $updatedValuesRules = [];

        if (!$this->isError)
            $updatedValuesRules[] = 'present';

        return [
            'isError' => ['boolean'],
            'description' => ['required_with:isError', 'string'],

            'updatedValueKeys.*' => ['string', 'distinct', Rule::in($validator->updatable_fields)],
            'updatedValues' => [
                ...$updatedValuesRules,
                'array',
                'min:' . count($validator->updatable_fields),
                'max:' . count($validator->updatable_fields),
            ],
            'updatedValues.*' => ['string'],
        ];
    }
}
