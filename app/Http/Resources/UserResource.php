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
        return [
            'id' => (string) $this->getKey(),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'family_id' => $this->family_id ? (string) $this->family_id : null,
            'role' => $this->role,
            'wallet_balance' => (int) $this->wallet_balance,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

