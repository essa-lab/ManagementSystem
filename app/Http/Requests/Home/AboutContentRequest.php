<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AboutContentRequest extends FormRequest
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
        return[ 
           
            'title_en'=>'nullable|string',
            'title_ku'=>'nullable|string',
            'title_ar'=>'nullable|string',
            'description_ku'=>'nullable|string',
            'description_ar'=>'nullable|string',
            'description_en'=>'nullable|string',
            'location_title_en'=>'nullable|string',
            'location_title_ar'=>'nullable|string',
            'location_title_ku'=>'nullable|string',
            'location_description_en'=>'nullable |string',
            'location_description_ar'=>'nullable |string',
            'location_description_ku'=>'nullable |string',
            'coordinates'=>'nullable |string',
        ];
    }
}
