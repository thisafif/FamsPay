<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class PreviewTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type'          => ['required', 'string', 'in:income,expense'],
            'amount'        => ['required', 'integer', 'min:1'],
            'txn_date'      => ['required', 'date', 'before_or_equal:today'],
            'category_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'txn_date.before_or_equal' => 'Transaction date cannot be in the future.',
            'amount.min'               => 'Amount must be greater than 0.',
        ];
    }
}
