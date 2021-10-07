<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $accountType = $this->route('account')->accountType;

        return [
            'description' => ['nullable', 'string'],
            'cost' => ['nullable', 'integer'],
            'price' => ['integer'],

            'images' => ['array'],
            'images.*' => ['image'],

            'tags' => ['array', 'min:1'],
            'tags.*' => ['array'],
            'tags.*.name' => ['required', 'string'],
            'tags.*.description' => ['nullable', 'string'],

            'creatorInfos' => ['array', 'min:' . $accountType->creatorAccountInfos()->count()],
            'creatorInfos.*' => ['array'],
            'creatorInfos.*.id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('account_infos', 'id')
                    ->where('account_type_id', $accountType->getKey())
                    ->where('can_creator', true)
            ],
            'creatorInfos.*.pivot' => ['required', 'array'],
            'creatorInfos.*.pivot.value' => ['required', 'string'],
        ];
    }
}
