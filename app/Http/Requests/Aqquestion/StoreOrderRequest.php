<?php
namespace App\Http\Requests\Aqquestion;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change based on user roles if needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'supplier_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'library_id' => 'required|numeric|exists:libraries,id',

        ];
    }
}
