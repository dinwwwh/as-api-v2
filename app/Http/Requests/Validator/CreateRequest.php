<?php

namespace App\Http\Requests\Validator;

use Illuminate\Foundation\Http\FormRequest;
use Str;

class CreateRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:validators,slug'],

            'description' => ['required', 'string'],
            'approverDescription' => ['required', 'string'],

            'readableFields' => ['present', 'array'],
            'readableFields.*' => ['string'],

            'updatableFields' => ['present', 'array'],
            'updatableFields.*' => ['string'],

            'users' => ['present', 'array'],
            'users.*.id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
