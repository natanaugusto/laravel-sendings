<?php

namespace App\Http\Requests;

class ContactStoreRequest extends ContactRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'spreadsheet_id' => 'nullable',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|max:50',
            'document' => 'nullable|max:20',
        ];
    }
}
