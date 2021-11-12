<?php

namespace App\Http\Requests;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Str;

class UpdateTagRequest extends FormRequest
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
            'oldSlug' => $this->route('tag')->getKey(),
        ]);

        if ($this->name)
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
            'name' => ['string'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', Rule::in([Tag::PROPERTY_TYPE, Tag::CATEGORY_TYPE])],
            'slug' => [
                'string',
                Rule::unique('tags', 'slug')
                    ->ignore($this->route('tag')->getKey(), 'slug')
            ],
            'mainImage' => ['image'],

            'parent' => ['nullable', 'array'],
            'parent.slug' => ['required_unless:parent,null', 'string', 'different:oldSlug', 'exists:tags,slug']
        ];
    }
}
