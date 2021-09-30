<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountTypeRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],

            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['array'],
            'tags.*.name' => ['required', 'string'],
            'tags.*.description' => ['nullable', 'string'],

            'users' => ['required', 'array', 'min:1'],
            'users.*' => ['array'],
            'users.*.id' => ['required', 'integer', 'exists:users,id']
        ];
    }
}
