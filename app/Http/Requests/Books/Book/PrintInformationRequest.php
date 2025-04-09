<?php

namespace App\Http\Requests\Books\Book;

use Illuminate\Foundation\Http\FormRequest;

class PrintInformationRequest extends FormRequest
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
            
            'publisher_ar' => 'nullable|string',
            'publisher_en' => 'nullable|string',
            'publisher_ku' => 'nullable|string',
            'print_house_ar' => 'nullable|string',
            'print_house_en' => 'nullable|string',
            'print_house_ku' => 'nullable|string',
            'print_location_ar' => 'nullable|string',
            'print_location_en' => 'nullable|string',
            'print_location_ku' => 'nullable|string',
            'print_year' => 'nullable|integer|min:1|max:9999',
            'year_type' => 'nullable|string|in:hijri,AD',

            'conditions' => ['nullable', 'array'],
            'conditions.*' => 'required|exists:print_conditions,id',

            'types' => ['nullable', 'array'],
            'types.*.id' => 'required|exists:print_types,id',
            'types.*.title_ar' => 'nullable|string',
            'types.*.title_en' => 'nullable|string',
            'types.*.title_ku' => 'nullable|string',

        ];
    }

}
