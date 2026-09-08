<?php

namespace App\Http\Requests\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupervisorRequest extends FormRequest
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
        $supervisor = $this->route('supervisor');

        return [
            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email,' . $supervisor->user_id,

            'company_id' => 'required|exists:companies,id',

            'phone' => 'required|string|max:20',

            'position' => 'required|string|max:100',

            'type' => 'required|in:sekolah,perusahaan',
        ];
    }
}