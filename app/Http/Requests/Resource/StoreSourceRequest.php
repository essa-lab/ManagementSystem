<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'source_id' => ['nullable', 'exists:sources,id'],
            'title_ar' => ['nullable', 'string'],
            'title_en' => ['nullable', 'string'],
            'title_ku' => ['nullable', 'string'],
        ];
    }
}
