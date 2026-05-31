<?php

namespace App\Http\Requests;

use App\Models\LeaveApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeaveUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date'   => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'reason'     => ['sometimes', 'string', 'max:1000'],
            'type'       => ['sometimes', Rule::in([
                LeaveApplication::TYPE_ANNUAL,
                LeaveApplication::TYPE_SICK,
                LeaveApplication::TYPE_UNPAID,
            ])],
        ];
    }
}
