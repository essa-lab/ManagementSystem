<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
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
            'title' => 'nullable|string',
            'registeration_number' => 'nullable|string',
            'language_id'=>'nullable|exists:languages,id',
            'page'=>'nullable|numeric',
            'limit'=>'nullable|numeric',
            'sortBy'=>'nullable|string|in:id',
            'sortOrder'=>'nullable|string|in:asc,desc',
            'identifier'=>'nullable|string'
        ];
    }

}
