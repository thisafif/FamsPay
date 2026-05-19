<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UserMonthlyLimit */
final class MonthlyLimitResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => (string) $this->getKey(),
            'user_id'              => (string) $this->user_id,
            'family_id'            => (string) $this->family_id,
            'period_month'         => $this->period_month,
            'monthly_limit_base'   => (int) $this->monthly_limit_base,
            'remaining_limit'      => (int) $this->remaining_limit,
            'total_expense_in_period' => (int) $this->total_expense_in_period,
            'total_income_in_period'  => (int) $this->total_income_in_period,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
        ];
    }
}
