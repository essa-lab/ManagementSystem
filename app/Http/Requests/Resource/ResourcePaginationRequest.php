<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourcePaginationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $rules = [
            
            'loadRelation'=>['nullable','string',function ($attribute, $value, $fail) {
                $allowedValues = [
                    'source','subjects','language','editors','medias','curators','curators.education',
                    'resourceable.articleType','resourceable.articleSpecification','resourceable.articleScientificClassification','resourceable.articleKeyword',
                    'resourceable.bookTranslateType','resourceable.bookPoetryCollection',
                    'resourceable.researchEducationLevel','resourceable.researchType','resourceable.researchFormat',
                    'resourceable.digitalType','resourceable.digitalFormat'
                ];
                $values = explode(',', $value);  
                foreach ($values as $val) {
                    if (!in_array(trim($val), $allowedValues)) {
                        $fail("The $attribute field contains an invalid value: $val.");
                    }
                }         
            }],

            'library_id' => ['nullable', 'exists:libraries,id'],
            'language_id' => ['nullable', 'exists:languages,id'],
            'search' => ['nullable', 'string'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'source' => ['nullable', 'array'],
            'source.id' => ['nullable', 'exists:sources,id'],
            'resourceable_type' => ['nullable', Rule::in(['book', 'article', 'research', 'digital_resource'])],
        ];
        switch ($this->input('resourceable_type','')) {
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
                $rules = array_merge($rules,$this->researchSpecificRules() );
                break;
            case 'digital_resource':
                $rules = array_merge($rules, $this->digitalSpecificRules());
                break;
        }
        return $rules;
    }
    private function articleSpecificRules(){
        return [
            'registeration_number' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:255',
            'article_scientific_classification_id' => 'nullable|exists:scientific_branches,id',
            'article_type_id' => 'nullable|exists:article_types,id',
            'article_specification_id' => 'nullable|exists:specifications,id',
        ];
    }
    private function bookSpecificRules(){
        return [
            'registeration_number' => 'nullable|string',
            'order_number' => 'nullable|string',
            'translator_type' => ['nullable', 'array'],
            'translator_type.*' => ['required', 'exists:translator_types,id'],
            'poetry_collection' => ['nullable', 'array'],
            'poetry_collection.*' => ['required', 'exists:poetry_collections,id'],

        ];
    }
    private function researchSpecificRules(){
        return [
            'registeration_number' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:255',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'research_type_id' => 'nullable|exists:research_types,id',
            'research_format_id' => 'nullable|exists:research_formats,id',
        ];
    }
    private function digitalSpecificRules(){
        return [
            'digital_format_id' => 'nullable|exists:digital_formats,id', 
            'digital_resource_type_id' => 'nullable|exists:digital_resource_types,id',
            'identifier' => 'nullable|string|max:255',
        ];
    }

}
