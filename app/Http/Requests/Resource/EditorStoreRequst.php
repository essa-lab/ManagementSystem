<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditorStoreRequst extends FormRequest
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
        return [
            '*.type' => ['nullable', 'string', Rule::in(['summary', 'abstract', 'note','conclusion'])],
            '*.language' => ['nullable', 'string', Rule::in(['en', 'ku', 'ar'])],
            '*.content' => ['nullable', 'string'],

        ];
    }
        
    

}
