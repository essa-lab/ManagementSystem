<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'library_id' => ['required', 'exists:libraries,id'],
            'language_id' => ['required', 'exists:languages,id'],
            'registry_date' => ['required', 'date'],
            'arrival_date' => ['required', 'date'],
            'number_of_copies' => ['required', 'integer', 'min:1'],
            'title_en' => ['required', 'string', 'required_without:title_ar,title_ku'],
            'title_ar' => ['required', 'string', 'required_without:title_en,title_ku'],
            'title_ku' => ['required', 'string', 'required_without:title_en,title_ar'],
            
            'resourceable_type' => ['required', Rule::in(['Book', 'Article', 'Research', 'DigitalResource'])],
        ];
        // $rules = [
       
       
        //     'editors' => ['nullable', 'array'],
        //     'editors.*.type' => ['required', 'string', Rule::in(['summary', 'abstract', 'note'])],
        //     'editors.*.language' => ['required', 'string', Rule::in(['en', 'ku', 'ar'])],
        //     'editors.*.content' => ['required', 'string'],

        
        //     'source' => ['nullable', 'array'],
        //     'source.id' => ['required', 'exists:sources,id'],
        //     'source.details' => ['array', 'nullable'],
        //     'source.details.title_ar' => ['nullable', 'string', 'required_without:source.details.title_ku,source.details.title_en'],
        //     'source.details.title_en' => ['nullable', 'string', 'required_without:source.details.title_ku,source.details.title_ar'],
        //     'source.details.title_ku' => ['nullable', 'string', 'required_without:source.details.title_ar,source.details.title_en'],

        



        // ];

    }

 



}
