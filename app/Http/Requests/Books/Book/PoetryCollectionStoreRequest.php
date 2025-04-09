<?php

namespace App\Http\Requests\Books\Book;

use Illuminate\Foundation\Http\FormRequest;

class PoetryCollectionStoreRequest extends FormRequest
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
            'poetry_collection_id' => ['nullable', 'exists:poetry_collections,id'],
            'name_ar' => ['nullable', 'string'],
            'name_en' => ['nullable', 'string'],
            'name_ku' => ['nullable', 'string'],
        ];
    }

}
