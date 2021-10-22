<?php

namespace App\Http\Requests;

use App\Models\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

            'tags' => ['array', 'min:1'],
            'tags.*' => ['array'],
            'tags.*.name' => ['required', 'string'],
            'tags.*.description' => ['nullable', 'string'],

            'users' => ['array', 'min:1'],
            'users.*' => ['array'],
            'users.*.id' => ['required', 'integer', 'exists:users,id'],


            /**
             * Validator relationship
             * Has potential security problem because not limit mappedReadableFields
             * => when approving validation approver can read unexpected sensitive infos
             * if mappedReadableFields contain invalid values
             *
             */
            'validators' => ['array'],
            'validators.*' => ['array'],
            'validators.*.id' => ['required', 'integer', 'exists:validators,id'],
            'validators.*.pivot' => ['required', 'array'],
            'validators.*.pivot.type' => ['present', Rule::in([
                Validator::CREATED_TYPE,
                Validator::UPDATED_TYPE,
                Validator::BOUGHT_TYPE,
            ])],

            'validators.*.pivot.mappedReadableFields' => ['present', 'array', 'keys:string'],
            'validators.*.pivot.mappedReadableFields.*' => [
                'integer',
                Rule::exists('account_infos', 'id')
                    ->where(
                        'account_type_id',
                        $this->route('accountType')
                            ->getKey()
                    )
            ],

            'validators.*.pivot.mappedUpdatableFields' => ['present', 'array', 'keys:string'],
            'validators.*.pivot.mappedUpdatableFields.*' => [
                'integer',
                Rule::exists('account_infos', 'id')
                    ->where(
                        'account_type_id',
                        $this->route('accountType')
                            ->getKey()
                    )
            ],
        ];
    }
}
