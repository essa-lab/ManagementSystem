<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ViewResourcesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return  [
            'limit'=>['nullable','numeric'],
            'page' => ['nullable','numeric'],
            'type'=>['string','required','in:Article,DigitalResource,Book,Research'],
            'status'=>['string','required','in:available,borrowed,reserved,lost,damaged']
        ];
    }
        
    

}
