<?php

namespace App\Http\Requests\Resource\ResourceCopy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceCopyStoreRequest extends FormRequest
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
            'resource_id' => ['required', 'numeric'],
            'status' => ['nullable', 'string','in:available,lost,damaged,borrowed,reserved'],
            'barcode' => ['nullable', 'string'],
            'shelf_number' => ['nullable', 'string'],
            'storage_location' => ['nullable', 'string'],
            'copy_number' => ['nullable', 'string',Rule::unique('resource_copies')->where(function ($query)  {
                return $query->where('resource_id', $this->resource_id);
            })],

        ];
    }

}
