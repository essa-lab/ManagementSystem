<?php

namespace App\Http\Requests\Resource\ResourceCopy;

use App\Models\Resource\Circulation;
use App\Models\Resource\ResourceCopy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RenewBook extends FormRequest
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
            'circulation_id' => ['required', 'numeric','exists:circulations,id', function ($attribute, $value, $fail) {
                // Check if there is at least one available copy for the given resource_id
                $availableCopy = Circulation::where('patron_id', auth('patron')->user()->id)->where('status','borrowed')
                    ->first();

                if (!$availableCopy) {
                    $fail('You cant renew this resource');
                }
            }],
            
        ];
    }

}
