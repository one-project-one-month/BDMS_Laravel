<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\HospitalResource;
use App\Http\Resources\Api\Admin\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="UserResource",
 * title="User Resource",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="roleId", type="integer", example=3),
 * @OA\Property(
 * property="role",
 * ref="#/components/schemas/RoleResource"
 * ),
 * @OA\Property(property="hospitalId", type="integer", example=1),
 * @OA\Property(
 * property="user",
 * ref="#/components/schemas/HospitalResource"
 * ),
 * @OA\Property(property="userName", type="string", example="Aung Aung"),
 * @OA\Property(property="email", type="string", format="email", example="aung@example.com"),
 * @OA\Property(property="isActive", type="boolean", example=true),
 * @OA\Property(property="createdAt", type="string", format="date-time", example="2026-03-01T10:00:00Z")
 * )
 */

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
            'userName' => $this->user_name,
            'email' => $this->email,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
