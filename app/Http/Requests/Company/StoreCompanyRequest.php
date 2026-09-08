<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'name'              => 'required|string|max:255',
            'address'           => 'nullable|string',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'supervisor_name'   => 'nullable|string|max:255',
        ];
    }
}