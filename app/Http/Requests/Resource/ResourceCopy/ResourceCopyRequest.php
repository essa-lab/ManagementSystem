<?php

namespace App\Http\Requests\Resource\ResourceCopy;

use Illuminate\Foundation\Http\FormRequest;

class ResourceCopyRequest extends FormRequest
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
            'status' => 'nullable|string|in:available,lost,damaged,borrowed,reserved',
            
            'page'=>'nullable|numeric',
            'limit'=>'nullable|numeric',
            'sortBy'=>'nullable|string|in:id',
            'sortOrder'=>'nullable|string|in:asc,desc',
            'loadRelation'=>'nullable|string|in:resource'
        ];
    }

}
