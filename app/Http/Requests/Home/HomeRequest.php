<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeRequest extends FormRequest
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
        return[ 
            'title_ar' => 'nullable|string',
            'title_en' => 'nullable|string',
            'title_ku' => 'nullable|string',
            'subtitle_ar' => 'nullable|string',
            'subtitle_en' => 'nullable|string',
            'subtitle_ku' => 'nullable|string',
            'asset' => 'nullable|string',
        ];
    }
}
