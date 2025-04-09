<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
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
/**
 * @OA\Schema(
 *     schema="UserFormRequest",
 *     required={"name", "email", "password", "status", "library_id", "privilages", "role"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *     @OA\Property(property="password", type="string", format="password", minLength=8, example="SecurePass123"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="profile_picture", type="string", nullable=true, example="https://example.com/profile.jpg"),
 *     @OA\Property(property="locale", type="string", enum={"en", "ar", "ku"}, nullable=true, example="en"),
 *     @OA\Property(property="library_id", type="integer", example=1),
 *     @OA\Property(
 *         property="privilages",
 *         type="array",
 *         @OA\Items(type="integer", example=5)
 *     ),
 *     @OA\Property(property="role", type="string", enum={"staff", "admin"}, example="admin")
 * )

 * @OA\Schema(
 *     schema="UpdateUserFormRequest",
 *     required={"name", "email", "password", "status", "library_id"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *     @OA\Property(property="password", type="string", format="password", minLength=8, example="SecurePass123"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="profile_picture", type="string", nullable=true, example="https://example.com/profile.jpg"),
 *     @OA\Property(property="locale", type="string", enum={"en", "ar", "ku"}, nullable=true, example="en"),
 *     @OA\Property(property="library_id", type="integer", example=1),
 *     @OA\Property(
 *         property="privilages",
 *         type="array",
 *         @OA\Items(type="integer", example=5)
 *     ),
 *     @OA\Property(property="role", type="string", enum={"staff", "admin"}, example="admin")
 * )
 */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'status'=>'required|string|in:active,inactive',
                'profile_picture'=>'nullable|string',
                'locale'=>'nullable|string|in:en,ar,ku',
                'library_id'=>'nullable|numeric|exists:libraries,id',
                'privilages'=>'nullable|array',
                'privilages.*'=>'numeric',
                'role'=>'required|string|in:staff,admin,super_admin'
         ];
        }
        if ($this->isMethod('put')) {
            return [
                'name' => 'nullable|string',
                'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->id)], 
                'password' => 'nullable|string|min:8',
                'status'=>'nullable|string|in:active,inactive',
                'profile_picture'=>'nullable|string',
                'locale'=>'nullable|string|in:en,ar,ku',
                'library_id'=>'nullable|numeric|exists:libraries,id',
                'privilages'=>'nullable|array',
                'privilages.*'=>'numeric',
                'role'=>'nullable|string|in:staff,admin,super_admin'
            ];
        }

        return [];
    }
}
