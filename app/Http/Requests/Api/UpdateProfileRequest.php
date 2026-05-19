<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
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
        $userId = $this->user()?->getKey();

        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($userId): void {
                    $email = strtolower((string) $value);
                    $existing = User::where('email', $email)->first();
                    if ($existing && (string) $existing->getKey() !== (string) $userId) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'max:72'],
        ];
    }
}

