<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Transaction */
final class TransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => (string) $this->getKey(),
            'user_id'       => (string) $this->user_id,
            'family_id'     => $this->family_id ? (string) $this->family_id : null,
            'type'          => $this->type,
            'amount'        => (int) $this->amount,
            'txn_date'      => $this->txn_date?->toIso8601String(),
            'category_name' => $this->category_name,
            'note'          => $this->note,
            'goal_id'       => $this->goal_id ? (string) $this->goal_id : null,
            'is_system'     => (bool) $this->is_system,
            'is_deleted'    => (bool) $this->is_deleted,
            'read_only'     => (bool) $this->goal_id || (bool) $this->is_system,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
