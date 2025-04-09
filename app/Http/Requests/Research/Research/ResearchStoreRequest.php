<?php

namespace App\Http\Requests\Research\Research;

use Illuminate\Foundation\Http\FormRequest;

class ResearchStoreRequest extends FormRequest
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
            'research_id'=>'required|exists:researches,id',
            'registeration_number' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:255',
            'publish_date' => 'nullable|date',
            'discussion_date' => 'nullable|date|after_or_equal:publish_date',
            'number_of_pages' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'classification' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'research_type_id' => 'nullable|exists:research_types,id',
            'research_format_id' => 'nullable|exists:research_formats,id',

            'university_en' => ['nullable', 'string'],
            'university_ar' => ['nullable', 'string'],
            'university_ku' => ['nullable', 'string'],

            'college_en' => ['nullable', 'string'],
            'college_ar' => ['nullable', 'string'],
            'college_ku' => ['nullable', 'string'],

            'education_major_en' => ['nullable', 'string'],
            'education_major_ar' => ['nullable', 'string'],
            'education_major_ku' => ['nullable', 'string'],
    ];
    }

}
