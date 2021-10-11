<?php

namespace App\Http\Requests\RechargedCard;

use App\Models\RechargedCard;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RechargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->service == null) {
            return config('settings.open_recharging_card');
        }

        if ($this->service == RechargedCard::THESIEURE_SERVICE) {
            return config('thesieure.open_recharging_card');
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'telco' => ['required', 'string'],
            'faceValue' => ['required', 'integer'],
            'serial' => ['required', 'string'],
            'code' => ['required', 'string'],
            'service' => ['nullable', Rule::in([RechargedCard::THESIEURE_SERVICE])],
        ];
    }
}
