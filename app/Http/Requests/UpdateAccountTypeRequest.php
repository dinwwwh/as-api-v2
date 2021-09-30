<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountTypeRequest extends FormRequest
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
            'name' => ['string'],
            'description' => ['nullable', 'string'],

            'tagNames' => ['array', 'min:1'],
            'tagNames.*' => ['string'],

            'userIds' => ['array', 'min:1'],
            'userIds.*' => ['integer', 'exists:users,id'],
        ];
    }
}
