<?php

namespace App\Http\Requests\Resource\LibrarySetting;

use Illuminate\Foundation\Http\FormRequest;

class LibrarySettingStoreRequest extends FormRequest
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
            'self_registeration' => 'nullable|numeric',
            'scheduler_time' => 'nullable|date_format:H:i|after_or_equal:00:00|before_or_equal:23:59',
        ];
    }

}
