<?php

namespace App\Http\Requests\Books\Book;

use Illuminate\Foundation\Http\FormRequest;

class BookStoreRequest extends FormRequest
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
            'book_id'=>'required|exists:books,id',
            'registeration_number' => 'nullable|string',

            'order_number' => 'nullable|string',

            'subtitle_en' => ['nullable', 'string'],
            'subtitle_ar' => ['nullable', 'string'],
            'subtitle_ku' => ['nullable', 'string'],

            'translated_title_en' => ['nullable', 'string'],
            'translated_title_ar' => ['nullable', 'string'],
            'translated_title_ku' => ['nullable', 'string'],

            'location_of_congress' => 'nullable|string',
            'dewey_decimal_classification' => 'nullable|string',

            'number_of_pages' => 'nullable|integer',
            'volume_number' => 'nullable|string',
            'volume' => 'nullable|string',
            'print_circulation' => 'nullable|string',
            'department' => 'nullable|string|',
            'table_of_content_condition' => 'nullable|string',
            'cover_specification' => 'nullable|string',
            'book_national_id_number' => 'nullable|string',

            'publishing_house_en' => ['nullable', 'string'],
            'publishing_house_ar' => ['nullable', 'string'],
            'publishing_house_ku' => ['nullable', 'string'],
            'isbn' => 'nullable|string|unique:books,isbn',
            'editor' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',

            // 'barcode' => 'nullable|string|max:255|unique:books,barcode',
        

           

        ];
    }

}
