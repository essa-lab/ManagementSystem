<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $rules = [
            'library_id' => ['required', 'exists:libraries,id'],
            'language_id' => ['required', 'exists:languages,id'],
            'uuid' => ['nullable', 'uuid'],
            'link' => ['nullable', 'string'],
            'registry_date' => ['nullable', 'date'],
            'arrival_date' => ['nullable', 'date'],
            'number_of_copies' => ['nullable', 'integer', 'min:1'],

            'title_en' => ['nullable', 'string', 'required_without:title_ar,title_ku'],
            'title_ar' => ['nullable', 'string', 'required_without:title_en,title_ku'],
            'title_ku' => ['nullable', 'string', 'required_without:title_en,title_ar'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],

            'editors' => ['nullable', 'array'],
            'editors.*.type' => ['required', 'string', Rule::in(['summary', 'abstract', 'note'])],
            'editors.*.language' => ['required', 'string', Rule::in(['en', 'ku', 'ar'])],
            'editors.*.content' => ['required', 'string'],

            'medias' => ['nullable', 'array'],
            'medias.*.type' => ['required', 'string', Rule::in(['scan', 'cover', 'file', 'source'])],
            'medias.*.path' => ['required', 'string'],

            'source' => ['nullable', 'array'],
            'source.id' => ['required', 'exists:sources,id'],
            'source.details' => ['array', 'nullable'],
            'source.details.title_ar' => ['nullable', 'string', 'required_without:source.details.title_ku,source.details.title_en'],
            'source.details.title_en' => ['nullable', 'string', 'required_without:source.details.title_ku,source.details.title_ar'],
            'source.details.title_ku' => ['nullable', 'string', 'required_without:source.details.title_ar,source.details.title_en'],

            'curators' => ['nullable', 'array'],
            'curators.*.type' => ['required', 'string', Rule::in(['author', 'supervisor', 'first_supervisor', 'second_supervisor', 'third_supervisor', 'discussion_committee', 'creator'])],
            'curators.*.name_ar' => ['nullable', 'string', 'required_without:curators.*.name_en,curators.*.name_ku'],
            'curators.*.name_en' => ['nullable', 'string', 'required_without:curators.*.name_ar,curators.*.name_ku'],
            'curators.*.name_ku' => ['nullable', 'string', 'required_without:curators.*.name_ar,curators.*.name_en'],


            // If resourceable_type is article or research, education_level must be required
            'curators.*.education_level' => [
                'nullable',
                'array',
            ],
            'curators.*.education_level.university_en' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.university_ku' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.university_ar' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.college_en' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.college_ku' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.college_ar' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.education_major_en' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.education_major_ku' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.education_major_ar' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.scientific_department_en' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.scientific_department_ku' => [
                'nullable',
                'string',
            ],
            'curators.*.education_level.scientific_department_ar' => [
                'nullable',
                'string',
            ],

            // 'resourceable_type' => ['required', Rule::in(['book', 'article', 'research', 'digital_resource'])],

            'resourceable' => ['required', 'array'],

        ];
        switch ($this->input('resourceable_type')) {
            case 'book':
                $rules = array_merge(
                    $rules,
                    $this->bookSpecificRules()
                );
                break;

            case 'article':
                $rules = array_merge($rules, $this->articleSpecificRules());
                break;

            case 'research':
                $rules = array_merge($rules, $this->researchSpecificRules());
                break;
            case 'digital_resource':
                $rules = array_merge($rules, $this->digitalSpecificRules());
                break;
        }
        return $rules;
    }
    private function articleSpecificRules()
    {
        return [

            'resourceable.registeration_number' => 'required|string|max:255',
            'resourceable.order_number' => 'nullable|string|max:255',

            'resourceable.subtitle_en' => ['nullable', 'string'],
            'resourceable.subtitle_ar' => ['nullable', 'string'],
            'resourceable.subtitle_ku' => ['nullable', 'string'],
            'resourceable.secondary_title_en' => ['nullable', 'string'],
            'resourceable.secondary_title_ar' => ['nullable', 'string'],
            'resourceable.secondary_title_ku' => ['nullable', 'string'],

            'resourceable.publication_date' => 'required|date',
            'resourceable.research_year' => 'nullable|integer|digits:4',
            'resourceable.duration_of_research' => 'nullable|string|max:255',
            'resourceable.course' => 'nullable|string|max:255',
            'resourceable.number' => 'nullable|string|max:255',
            'resourceable.number_of_pages' => 'nullable|integer',
            'resourceable.map' => 'nullable|boolean',
            'resourceable.place_of_printing' => 'nullable|string|max:255',
            'resourceable.journal_name' => 'nullable|string|max:255',
            'resourceable.journal_volume' => 'nullable|string|max:255',

            'resourceable.article_scientific_classification_id' => 'nullable|exists:scientific_branches,id',
            'resourceable.article_type_id' => 'nullable|exists:article_types,id',
            'resourceable.article_specification_id' => 'nullable|exists:specifications,id',

            'resourceable.keywords' => ['nullable', 'array'],
            'resourceable.keywords.*.title_ku' => ['nullable', 'string'],
            'resourceable.keywords.*.title_ar' => ['nullable', 'string'],
            'resourceable.keywords.*.title_en' => ['nullable', 'string'],


        ];
    }
    private function bookSpecificRules()
    {
        info($this->input('resourceable_id'));
        return [
            'resourceable.registeration_number' => 'required|string',

            'resourceable.order_number' => 'nullable|string',

            'resourceable.subtitle_en' => ['nullable', 'string', 'required_without:subtitle_ar,subtitle_ku'],
            'resourceable.subtitle_ar' => ['nullable', 'string', 'required_without:subtitle_en,subtitle_ku'],
            'resourceable.subtitle_ku' => ['nullable', 'string', 'required_without:subtitle_en,subtitle_ar'],

            'resourceable.translated_title_en' => ['nullable', 'string', 'required_without:translated_title_ar, translated_title_ku'],
            'resourceable.translated_title_ar' => ['nullable', 'string', 'required_without:translated_title_en, translated_title_ku'],
            'resourceable.translated_title_ku' => ['nullable', 'string', 'required_without:translated_title_en, translated_title_ar'],

            'resourceable.location_of_congress' => 'nullable|string',
            'resourceable.dewey_decimal_classification' => 'nullable|string',

            'resourceable.number_of_pages' => 'nullable|integer',
            'resourceable.volume_number' => 'nullable|string',
            'resourceable.volume' => 'nullable|string',
            'resourceable.print_circulation' => 'nullable|string',
            'resourceable.department' => 'nullable|string|',
            'resourceable.table_of_content_condition' => 'nullable|string',
            'resourceable.cover_specification' => 'nullable|string',
            'resourceable.book_national_id_number' => 'nullable|string',

            'resourceable.publishing_house_en' => ['nullable', 'string', 'required_without:publishing_house_ar,publishing_house_ku'],
            'resourceable.publishing_house_ar' => ['nullable', 'string', 'required_without:publishing_house_en,publishing_house_ku'],
            'resourceable.publishing_house_ku' => ['nullable', 'string', 'required_without:publishing_house_en,publishing_house_ar'],

            'resourceable.price' => 'nullable|numeric|min:0',
            'resourceable.barcode' => ['nullable','string',Rule::unique('books', 'barcode')->ignore($this->input('resourceable_id'))
        ],
            'resourceable.isbn' => ['nullable','string',Rule::unique('books', 'isbn')->ignore($this->input('resourceable_id'))],
            'resourceable.editor' => 'nullable|string|max:255',

            'resourceable.translator_type' => ['nullable', 'array'],
            'resourceable.translator_type.*.id' => ['required', 'exists:translate_types,id'],
            'resourceable.translator_type.*.book_translators' => ['array', 'nullable'],
            'resourceable.translator_type.*.book_translators.name_ar' => ['nullable', 'string'],
            'resourceable.translator_type.*.book_translators.name_en' => ['nullable', 'string'],
            'resourceable.translator_type.*.book_translators.name_ku' => ['nullable', 'string'],

            'resourceable.poetry_collection' => ['nullable', 'array'],
            'resourceable.poetry_collection.id' => ['required', 'exists:poetry_collections,id'],
            'resourceable.poetry_collection.name' => ['array', 'nullable'],
            'resourceable.poetry_collection.name.name_ar' => ['nullable', 'string', 'required_without:poetry_collection.name.name_ku,poetry_collection.name.name_en'],
            'resourceable.poetry_collection.name.name_en' => ['nullable', 'string', 'required_without:poetry_collection.name.name_ku,poetry_collection.name.name_ar'],
            'resourceable.poetry_collection.name.name_ku' => ['nullable', 'string', 'required_without:poetry_collection.name.name_ar,poetry_collection.name.name_en'],

            'resourceable.print_information' => ['nullable', 'array'],
            'resourceable.print_information.publisher_ar' => 'nullable|string',
            'resourceable.print_information.publisher_en' => 'nullable|string',
            'resourceable.print_information.publisher_ku' => 'nullable|string',
            'resourceable.print_information.print_house_ar' => 'nullable|string',
            'resourceable.print_information.print_house_en' => 'nullable|string',
            'resourceable.print_information.print_house_ku' => 'nullable|string',
            'resourceable.print_information.print_location_ar' => 'nullable|string',
            'resourceable.print_information.print_location_en' => 'nullable|string',
            'resourceable.print_information.print_location_ku' => 'nullable|string',
            'resourceable.print_information.print_year' => 'nullable|integer|digits:4',
            'resourceable.print_information.year_type' => 'nullable|string|in:hijri,AD',

            'resourceable.print_information.conditions' => ['nullable', 'array'],
            'resourceable.print_information.conditions.*' => 'required|exists:print_conditions,id',

            'resourceable.print_information.types' => ['nullable', 'array'],
            'resourceable.print_information.types.*.id' => 'required|exists:print_types,id',
            'resourceable.print_information.types.*.title_ar' => 'nullable|string',
            'resourceable.print_information.types.*.title_en' => 'nullable|string',
            'resourceable.print_information.types.*.title_ku' => 'nullable|string',

            'resourceable.specific_subject' => ['nullable', 'array'],
            'resourceable.specific_subject.*.title_ku' => ['nullable', 'string'],
            'resourceable.specific_subject.*.title_ar' => ['nullable', 'string'],
            'resourceable.specific_subject.*.title_en' => ['nullable', 'string'],


        ];
    }
    private function researchSpecificRules()
    {
        return [
            'resourceable.registeration_number' => 'required|string|max:255',
            'resourceable.order_number' => 'nullable|string|max:255',
            'resourceable.publish_date' => 'nullable|date',
            'resourceable.discussion_date' => 'nullable|date|after_or_equal:publish_date',
            'resourceable.number_of_pages' => 'nullable|integer|min:1',
            'resourceable.price' => 'nullable|numeric|min:0',
            'resourceable.classification' => 'nullable|string|max:255',
            'resourceable.status' => 'nullable|string|max:255',
            'resourceable.education_level_id' => 'nullable|exists:education_levels,id',
            'resourceable.research_type_id' => 'nullable|exists:research_types,id',
            'resourceable.research_format_id' => 'nullable|exists:research_formats,id',

            'resourceable.university_en' => ['nullable', 'string'],
            'resourceable.university_ar' => ['nullable', 'string'],
            'resourceable.university_ku' => ['nullable', 'string'],

            'resourceable.college_en' => ['nullable', 'string'],
            'resourceable.college_ar' => ['nullable', 'string'],
            'resourceable.college_ku' => ['nullable', 'string'],

            'resourceable.education_major_en' => ['nullable', 'string'],
            'resourceable.education_major_ar' => ['nullable', 'string'],
            'resourceable.education_major_ku' => ['nullable', 'string'],

            'resourceable.keywords' => ['nullable', 'array'],
            'resourceable.keywords.*.title_ku' => ['nullable', 'string'],
            'resourceable.keywords.*.title_ar' => ['nullable', 'string'],
            'resourceable.keywords.*.title_en' => ['nullable', 'string'],

        ];
    }
    private function digitalSpecificRules()
    {
        return [
            'resourceable.digital_format_id' => 'nullable|exists:digital_formats,id',
            'resourceable.digital_resource_type_id' => 'nullable|exists:digital_resource_types,id',
            'resourceable.identifier' => 'nullable|string|max:255',
            'resourceable.publisher' => 'nullable|string|max:255',
            'resourceable.coverage' => 'nullable|string|max:255',

            'resourceable.right' => ['nullable', 'array'],
            'resourceable.right.title_ku' => ['nullable', 'string'],
            'resourceable.right.title_ar' => ['nullable', 'string'],
            'resourceable.right.title_en' => ['nullable', 'string'],

            'resourceable.relations' => ['nullable', 'array'],
            'resourceable.relations.*.title_ku' => ['nullable', 'string'],
            'resourceable.relations.*.title_ar' => ['nullable', 'string'],
            'resourceable.relations.*.title_en' => ['nullable', 'string'],

            'resourceable.specific_subject' => ['nullable', 'array'],
            'resourceable.specific_subject.title_ku' => ['nullable', 'string'],
            'resourceable.specific_subject.title_ar' => ['nullable', 'string'],
            'resourceable.specific_subject.title_en' => ['nullable', 'string'],

        ];
    }

}
