<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferMoneyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_account_id' => ['required', 'integer', 'exists:accounts,id'],
            'to_account_id' => ['required', 'integer', 'exists:accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_account_id.required' => 'Source account ID is required',
            'from_account_id.exists' => 'Source account does not exist',
            'to_account_id.required' => 'Destination account ID is required',
            'to_account_id.exists' => 'Destination account does not exist',
            'to_account_id.different' => 'Destination account must be different from source account',
            'amount.required' => 'Transfer amount is required',
            'amount.numeric' => 'Transfer amount must be a number',
            'amount.min' => 'Transfer amount must be greater than 0',
        ];
    }
}
