<?php

namespace App\Http\Requests\Books\Book;

use Illuminate\Foundation\Http\FormRequest;

class SpecificSubjectStoreRequest extends FormRequest
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
            '*.id'=>'nullable|exists:book_specific_subjects,id',
            '*.title_ku' => ['nullable', 'string'],
            '*.title_ar' => ['nullable', 'string'],
            '*.title_en' => ['nullable', 'string'],
        ];
    }

}
