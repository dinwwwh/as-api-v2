<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountInfoRequest extends FormRequest
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
            'canCreator' => ['boolean'],
            'canBuyer' => ['boolean'],
            'canBuyerOke' => ['boolean'],

            'rules' => ['array'],
            'rules.*' => ['array'],
            'rules.*.key' => ['required', 'string', 'exists:rules,key'],
        ];
    }
}
