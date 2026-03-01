<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\HospitalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'role' => new RoleResource($this->whenLoaded('role')),
            'hospitalId' => $this->hospital_id,
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'user_name' => $this->user_name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
