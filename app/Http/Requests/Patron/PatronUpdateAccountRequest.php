<?php

namespace App\Http\Requests\Patron;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PatronUpdateAccountRequest extends FormRequest
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
            'name' => 'nullable|string',
            'internal_identifier'=>'nullable|string',
            'occupation' => 'nullable|string',
            'phone'=>['nullable','string',Rule::unique( 'patrons','phone')->ignore(auth('patron')->user()->id)],
            'address'=>'nullable|string',
            'status'=>'nullable|string|in:active,inactive',
            'locale'=>'nullable|string',
            'profile_picture'=>'nullable|string',
            'university'=>['nullable','string'],
                'college'=>['nullable','string'],


        ];
    }

}
