<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Goal */
final class GoalResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => (string) $this->getKey(),
            'user_id'        => (string) $this->user_id,
            'family_id'      => $this->family_id ? (string) $this->family_id : null,
            'title'          => $this->title,
            'target_amount'  => (int) $this->target_amount,
            'current_amount' => (int) $this->current_amount,
            'status'         => $this->status,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
