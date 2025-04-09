<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function validationData(){
        return $this->json()->all();
    }
    public function rules(): array
    {
        return  [
            '*.id'=>'nullable|exists:curators,id',

            '*.type' => ['required', 'string', Rule::in(['author'])],
            '*.name_ar' => ['nullable', 'string'],
            '*.name_en' => ['nullable', 'string'],
            '*.name_ku' => ['nullable', 'string'],


            // If resourceable_type is article or research, education_level must be required
            '*.education_level' => [
                'nullable',
                'array',
            ],
            '*.education_level.university_en' => [
                'nullable',
                'string',
            ],
            '*.education_level.university_ku' => [
                'nullable',
                'string',
            ],
            '*.education_level.university_ar' => [
                'nullable',
                'string',
            ],
            '*.education_level.college_en' => [
                'nullable',
                'string',
            ],
            '*.education_level.college_ku' => [
                'nullable',
                'string',
            ],
            '*.education_level.college_ar' => [
                'nullable',
                'string',
            ],
            '*.education_level.education_major_en' => [
                'nullable',
                'string',
            ],
            '*.education_level.education_major_ku' => [
                'nullable',
                'string',
            ],
            '*.education_level.education_major_ar' => [
                'nullable',
                'string',
            ],
            '*.education_level.scientific_department_en' => [
                'nullable',
                'string',
            ],
            '*.education_level.scientific_department_ku' => [
                'nullable',
                'string',
            ],
            '*.education_level.scientific_department_ar' => [
                'nullable',
                'string',
            ]
            
        ];
    }
        
    

}
