<?php

namespace App\Http\Requests\RechargedCard;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'realFaceValue' => ['required', 'integer'],
            'receivedValue' => ['required', 'integer', 'max:' . $this->realFaceValue],
        ];
    }
}
