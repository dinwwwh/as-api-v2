<?php

namespace App\Http\Requests\Validator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Str;

class UpdateRequest extends FormRequest
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
        if ($this->name)
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
    }

    /**
     * Get the validator rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string'],
            'slug' => [
                'string',
                Rule::unique('validators', 'slug')
                    ->ignore($this->route('validator')->slug, 'slug')
            ],

            'fee' => ['integer', 'min:0'],

            'description' => ['string'],
            'approverDescription' => ['string'],

            'readableFields' => ['array'],
            'readableFields.*' => ['string'],

            'updatableFields' => ['array'],
            'updatableFields.*' => ['string'],
        ];
    }
}
