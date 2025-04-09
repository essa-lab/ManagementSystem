<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaStoreRequest extends FormRequest
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
            '*.id'=>'nullable|exists:medias,id',
            '*.type' => ['nullable', 'string', Rule::in(['scan', 'cover', 'file', 'source'])],
            '*.path' => ['nullable', 'string'],
        ];
    }
        
    

}
