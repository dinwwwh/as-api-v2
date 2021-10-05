<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => ['nullable', 'string'],
            'cost' => ['nullable', 'integer'],
            'price' => ['required', 'integer'],

            'images' => ['required', 'array'],
            'images.*' => ['image'],

            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['array'],
            'tags.*.name' => ['required', 'string'],
            'tags.*.description' => ['nullable', 'string'],
        ];
    }
}
