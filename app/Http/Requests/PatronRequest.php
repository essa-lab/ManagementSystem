<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatronRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
           
            return [
                'internal_identifier' => ['nullable', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('patrons', 'email')], 
                'password' => ['required', 'string', 'min:8', 'confirmed'], 
                'occupation' => ['nullable', 'string', 'max:255'],
                'locale' => ['nullable', 'string', 'in:en,ar,ku'], 
                'university'=>['nullable','string'],
                'college'=>['nullable','string'],

                'phone' => ['nullable', 'string'], 
                'address' => ['nullable', 'string', 'max:500'],
                'status' => ['required', Rule::in(['active', 'inactive'])] 
            ];
        }
        if ($this->isMethod('put')) {
            return [
                'internal_identifier' => ['nullable', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('patrons', 'email')->ignore($this->id)], 
                'password' => ['required', 'string', 'min:8', 'confirmed'], 

                'occupation' => ['nullable', 'string', 'max:255'],
                'locale' => ['nullable', 'string', 'in:en,ar,ku'], 
                'phone' => ['nullable', 'string'], 
                'address' => ['nullable', 'string', 'max:500'],
                'status' => ['required', Rule::in(['active', 'inactive'])] 
            ];
        }

        return [];
    }
}
