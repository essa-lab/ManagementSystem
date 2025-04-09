<?php

namespace App\Http\Requests\Patron;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatronStoreRequest extends FormRequest
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
            'name' => 'required|string',
            'internal_identifier'=>'nullable|string',
            'university'=>['nullable','string'],
                'college'=>['nullable','string'],
            'email'=>'required|string|email|unique:patrons,email',
            'phone'=>['nullable','string',Rule::unique( 'patrons','phone')],
            'address'=>'nullable|string',
            'password'=>'required|string|min:8',
            'status'=>'required|string|in:active,inactive',
            'locale'=>'nullable|string',
            'occupation'=>'nullable|string'
        ];
    }

}
