<?php

namespace App\Http\Requests\Resource\Library;

use Illuminate\Foundation\Http\FormRequest;

class LibraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'loadRelation' => 'nullable|string|in:users,settings',
            'page'=>'nullable|numeric',
            'limit'=>'nullable|numeric',
            'sortBy'=>'nullable|string|in:id',
            'sortOrder'=>'nullable|string|in:asc,desc',
        ];
    }

}
