<?php

namespace App\Http\Requests\DigitalResource\DigitalResource;

use Illuminate\Foundation\Http\FormRequest;

class DigitalResourceStoreRequest extends FormRequest
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
            'digital_resource_id'=>'required|exists:digital_resources,id',
            'digital_format_id' => 'nullable|exists:digital_formats,id',
            'digital_resource_type_id' => 'nullable|exists:digital_resource_types,id',
            'identifier' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'coverage' => 'nullable|string|max:255',


    ];
    }

}
