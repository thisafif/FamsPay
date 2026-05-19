<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class AllocateGoalRequest extends FormRequest
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
            'amount'  => ['required', 'integer', 'min:1'],
            'confirm' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Amount must be greater than 0.',
        ];
    }
}
