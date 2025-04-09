<?php

namespace App\Http\Requests\Patron;

use Illuminate\Foundation\Http\FormRequest;

class PatronRequest extends FormRequest
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
            'search' => 'nullable|string',
            'status'=>'nullable|string|in:active,inactive',
            'page'=>'nullable|numeric',
            'limit'=>'nullable|numeric',
            'sortBy'=>'nullable|string|in:id,status,role',
            'sortOrder'=>'nullable|string|in:asc,desc',
        ];
    }

}
