<?php

namespace App\Http\Requests\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupervisorRequest extends FormRequest
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
            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email',

            'password' => 'required|string|min:8|confirmed',

            'company_id' => 'required|exists:companies,id',

            'phone' => 'required|string|max:20',

            'position' => 'required|string|max:100',

            'type' => 'required|in:sekolah,perusahaan',
        ];
    }
}