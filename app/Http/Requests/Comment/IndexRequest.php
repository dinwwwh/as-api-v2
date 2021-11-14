<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
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
            '_commentableType' => ['required_with:_commentableId', 'string'],
            '_commentableId' => ['required_with:_commentableType', 'integer'],
            '_orderBy' => ['string', Rule::in(['DESC', 'desc', 'ASC', 'asc'])],
        ];
    }
}
