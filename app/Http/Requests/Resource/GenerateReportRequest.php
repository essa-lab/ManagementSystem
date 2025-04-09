<?php
namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return  [
            'format_type'=>'required|string|in:pdf,excel',
            'report_type'=>'required|string|in:available,borrow,popular,overdue,damaged,lost',
            'from' => [
                Rule::requiredIf($this->report_type !== 'popular'),
                'date'
            ],
            'to' => [
                Rule::requiredIf($this->report_type !== 'popular'),
                'date'
            ],

        ];
    }
}
