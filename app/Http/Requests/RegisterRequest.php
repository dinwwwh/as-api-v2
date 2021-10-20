<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['required', 'email', 'unique:users'],
            'login' => ['required', 'string', 'min:6', 'max:36', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
