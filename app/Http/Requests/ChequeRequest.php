<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChequeRequest extends FormRequest
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
        $chequeId = $this->cheque ? $this->cheque->id : null;

        return [
            'check_number' => 'required|string|unique:cheques,check_number,' . $chequeId,
            'amount' => 'required|numeric|min:1',
            'beneficiary' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'check_number.required' => 'Check number is required.',
            'check_number.string' => 'Check number must be a valid string.',
            'check_number.unique' => 'This check number is already in use. Please use a different one.',

            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a numeric value.',
            'amount.min' => 'Amount must be at least 1.',

            'beneficiary.required' => 'Beneficiary is required.',
            'beneficiary.string' => 'Beneficiary must be a valid string.',
            'beneficiary.max' => 'Beneficiary cannot exceed 255 characters.',
        ];
    }
}
