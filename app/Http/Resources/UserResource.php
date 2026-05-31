<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $period      = now()->format('Y-m');
        $limitRecord = \App\Models\UserMonthlyLimit::where('user_id', (string) $this->getKey())
            ->where('period_month', $period)
            ->first();

        return [
            'id' => (string) $this->getKey(),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'family_id' => $this->family_id ? (string) $this->family_id : null,
            'role' => $this->role,
            'wallet_balance' => (int) $this->wallet_balance,
            'monthly_limit_base' => $limitRecord ? (int) $limitRecord->monthly_limit_base : 0,
            'remaining_limit' => $limitRecord ? (int) $limitRecord->remaining_limit : 0,
            'current_month_expense' => $limitRecord ? (int) $limitRecord->total_expense_in_period : 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

