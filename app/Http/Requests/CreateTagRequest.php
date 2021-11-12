<?php

namespace App\Http\Requests;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Str;

class CreateTagRequest extends FormRequest
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
     * Prepare data for validation
     *
     */
    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
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
            'slug' => ['unique:tags,slug'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', Rule::in([Tag::PROPERTY_TYPE, Tag::CATEGORY_TYPE])],
            'mainImage' => ['image'],

            'parent' => ['nullable', 'array'],
            'parent.slug' => ['required_unless:parent,null', 'string', 'exists:tags,slug'],
        ];
    }
}
