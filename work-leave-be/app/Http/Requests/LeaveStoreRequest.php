<?php

namespace App\Http\Requests;

use App\Models\LeaveApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeaveStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'reason'     => ['required', 'string', 'max:1000'],
            'type'       => ['required', Rule::in([
                LeaveApplication::TYPE_ANNUAL,
                LeaveApplication::TYPE_SICK,
                LeaveApplication::TYPE_UNPAID,
            ])],
        ];
    }
}
