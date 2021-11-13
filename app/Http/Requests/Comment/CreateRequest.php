<?php

namespace App\Http\Requests\Comment;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->validate([
            'commentableId' => ['required', 'integer'],
            'commentableType' => ['required', 'string'],
        ]);

        $commentableClass = Relation::getMorphedModel($this->commentableType) ?? $this->commentableType;
        $commentable = $commentableClass::find($this->commentableId);
        if (!$commentable) {
            return false;
        }

        $this->merge(['commentable' => $commentable]);

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
            'content' => ['required', 'string', 'min:5']
        ];
    }
}
