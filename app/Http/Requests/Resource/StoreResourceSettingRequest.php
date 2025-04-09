<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return  [
            'resource_id'=>'required|exists:resources,id',
            'availability'=>'nullable|boolean',
            'max_allowed_day'=>'nullable|integer',
            'allow_renewal'=>'nullable|boolean',
            'locked'=>'nullable|boolean',
            'renewal_cycle'=>'nullable|integer'
        ];
    }
        
    

}
