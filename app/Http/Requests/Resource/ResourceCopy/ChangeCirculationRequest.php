<?php

namespace App\Http\Requests\Resource\ResourceCopy;

use App\Models\Resource\Circulation;
use App\Models\Resource\CirculationLog;
use App\Models\Resource\ResourceCopy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeCirculationRequest extends FormRequest
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
            'circulation_id'=>'required|numeric|exists:circulations,id',
'status' => [
        'required',
        'string',
        Rule::in(['request_rejected', 'request_approved']),
        function ($attribute, $value, $fail)  {
            $circulation = CirculationLog::where('circulation_id',$this->circulation_id)->latest()->first();

            if (!$circulation) {
                $fail('Invalid circulation record.');
                return;
            }

            $currentStatus = $circulation->status;

            $validTransitions = [
                'request_submitted' => ['request_rejected', 'request_approved'],
            ];
            

            if (!isset($validTransitions[$currentStatus]) || !in_array($value, $validTransitions[$currentStatus])) {
                $fail("Invalid status transition from $currentStatus to $value.");
            }
        }
    ],       
 ];
    }

}
