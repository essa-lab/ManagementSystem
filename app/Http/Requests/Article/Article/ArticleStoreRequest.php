<?php

namespace App\Http\Requests\Article\Article;

use Illuminate\Foundation\Http\FormRequest;

class ArticleStoreRequest extends FormRequest
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

        'article_id'=>'required|exists:articles,id',
        'registeration_number' => 'nullable|string|max:255',
        'order_number' => 'nullable|string|max:255',

        'subtitle_en' => ['nullable', 'string'],
        'subtitle_ar' => ['nullable', 'string'],
        'subtitle_ku' => ['nullable', 'string'],
        'secondary_title_en' => ['nullable', 'string'],
        'secondary_title_ar' => ['nullable', 'string'],
        'secondary_title_ku' => ['nullable', 'string'],

        'publication_date' => 'nullable|date',
        'research_year' => 'nullable|integer|digits:4',
        'duration_of_research' => 'nullable|string|max:255',
        'course' => 'nullable|string|max:255',
        'number' => 'nullable|string|max:255',
        'number_of_pages' => 'nullable|integer',
        'map' => 'nullable|boolean',
        'place_of_printing_en'=>        'nullable|string|max:255',

        'place_of_printing_ar'=>        'nullable|string|max:255',

        'place_of_printing_ku'=>        'nullable|string|max:255',

        'journal_name' => 'nullable|string|max:255',
        'journal_volume' => 'nullable|string|max:255',

        'article_scientific_classification_id' => 'nullable|exists:scientific_branches,id',
        'article_type_id' => 'nullable|exists:article_types,id',
        'article_specification_id' => 'nullable|exists:specifications,id',

        // 'keywords' => ['nullable', 'array'],
        // 'keywords.*.title_ku' => ['nullable', 'string'],
        // 'keywords.*.title_ar' => ['nullable', 'string'],
        // 'keywords.*.title_en' => ['nullable', 'string'],


    ];
    }

}
