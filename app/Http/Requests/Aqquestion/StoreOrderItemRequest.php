<?php
namespace App\Http\Requests\Aqquestion;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.type' => 'required|string|in:Article,Book,Research,DigitalResearch',
            'items.*.author' => 'nullable|string|max:255',
            'items.*.isbn' => 'nullable|string|max:20',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }
}
