<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ShowUserRequest extends FormRequest
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
            'loadRelation'=>['nullable','string',function ($attribute, $value, $fail) {
                $allowedValues = ['library', 'privilages'];
                $values = explode(',', $value);  
                foreach ($values as $val) {
                    if (!in_array(trim($val), $allowedValues)) {
                        $fail("The $attribute field contains an invalid value: $val.");
                    }
                }         
            }],
        ];

    }
}
