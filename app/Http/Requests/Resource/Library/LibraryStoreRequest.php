<?php

namespace App\Http\Requests\Resource\Library;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LibraryStoreRequest extends FormRequest
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
            'name_en' => ['required', 'string'],
            'name_ar' => ['nullable', 'string'],
            'name_ku' => ['nullable', 'string'],
            
            'location'=>'required|string',
            'logo'=>'nullable|string',
        ];
    }

}
