<?php

namespace App\Http\Requests;

class MessageStoreRequest extends MessageRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'exists:App\Models\User,id',
            'subject' => 'required|max:255',
            'body' => 'required',
            'attaches.*' => 'nullable|file'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['user_id' => auth()->user()->id]);
        parent::prepareForValidation();
    }
}
