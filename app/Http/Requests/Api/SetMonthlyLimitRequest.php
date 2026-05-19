<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class SetMonthlyLimitRequest extends FormRequest
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
            'monthly_limit_base' => ['required', 'integer', 'min:0'],
            'period_month'       => ['nullable', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'period_month.regex' => 'The period_month must be in YYYY-MM format (e.g. 2026-05).',
        ];
    }
}
