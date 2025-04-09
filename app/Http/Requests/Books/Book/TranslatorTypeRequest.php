<?php

namespace App\Http\Requests\Books\Book;

use Illuminate\Foundation\Http\FormRequest;

class TranslatorTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function validationData(){
        return $this->json()->all();
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.id'=>'nullable|exists:book_translator,id',
            '*.translate_type_id' => ['required', 'exists:translate_types,id'],
            '*.name_ar' => ['nullable', 'string'],
            '*.name_en' => ['nullable', 'string'],
            '*.name_ku' => ['nullable', 'string'],
        ];
    }

}
