<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'roleId' => $this->role_id,
            'hospitalId' => $this->hospital_id,
            'userName' => $this->user_name,
            'email' => $this->email,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at,
        ];
    }
}
