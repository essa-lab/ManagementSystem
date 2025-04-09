<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
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
                'email'=>['nullable','email',Rule::unique('users','email')->ignore(Auth::user()->id)],
                'password' => 'nullable|string|min:8',
                'profile_picture'=>'nullable|string',
                'locale'=>'nullable|string|in:en,ar,ku',
            ];


    }
}
