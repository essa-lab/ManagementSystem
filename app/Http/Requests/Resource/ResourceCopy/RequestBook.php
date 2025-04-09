<?php

namespace App\Http\Requests\Resource\ResourceCopy;

use App\Models\Resource\ResourceCopy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestBook extends FormRequest
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
            'resource_id' => ['required', 'numeric','exists:resources,id', function ($attribute, $value, $fail) {
                // Check if there is at least one available copy for the given resource_id
                $availableCopy = ResourceCopy::where('resource_id', $value)
                    ->where('status', 'available')
                    ->exists();

                if (!$availableCopy) {
                    $fail('No available copies for this resource.');
                }
            }],
            
        ];
    }

}
