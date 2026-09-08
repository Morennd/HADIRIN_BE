<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'attendance_date' => 'required|date',

            'check_in' => [
                'required',
                'regex:/^(?:[01]\d|2[0-3])([:.])([0-5]\d)(?::([0-5]\d))?$/',
            ],

            'check_out' => [
                'nullable',
                'regex:/^(?:[01]\d|2[0-3])([:.])([0-5]\d)(?::([0-5]\d))?$/',
            ],

            'notes' => 'nullable|string|max:1000',

            'documentation' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }
}