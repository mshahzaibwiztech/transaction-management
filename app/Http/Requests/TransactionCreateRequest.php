<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionCreateRequest extends FormRequest
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
            'amount' => 'required|numeric',
            'user_id' => 'required|exists:users,id,user_type,CUSTOMER',
            'due_on' => 'required|date|after:date',
            'vat' => 'required|numeric|min:0',
            'is_vat_inclusive' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'user_id not found, user must be a customer.'
        ];
    }
}
