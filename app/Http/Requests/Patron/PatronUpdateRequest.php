<?php

namespace App\Http\Requests\Patron;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatronUpdateRequest extends FormRequest
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
            'name' => 'nullable|string',
            'internal_identifier'=>'nullable|string',
            'occupation' => 'nullable|string|',
            'email'=>['nullable','email',Rule::unique('patrons')->ignore($this->route('patron'))],
            'phone'=>['nullable','string',Rule::unique( 'patrons','phone')->ignore($this->route('patron'))],
            'address'=>'nullable|string',
            'password'=>'nullable|string|min:8',

            'status'=>'nullable|string|in:active,inactive',
            'locale'=>'nullable|string',
            'university'=>['nullable','string'],
            'college'=>['nullable','string'],
            'verified'=>['nullable','boolean']
        ];
    }

}
