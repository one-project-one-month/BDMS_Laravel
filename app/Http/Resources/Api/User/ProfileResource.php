<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="ProfileUserResource",
 * title="Profile User Resource (User Only)",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="roleId", type="integer", example=3),
 * @OA\Property(property="hospitalId", type="integer", example=1),
 * @OA\Property(property="userName", type="string", example="John Doe"),
 * @OA\Property(property="email", type="string", example="johndoe@example.com"),
 * @OA\Property(property="isActive", type="boolean", example="true"),
 * @OA\Property(property="createdAt", type="boolean", example="true"),
 * @OA\Property(
 * property="donorInfo",
 * ref="#/components/schemas/ProfileDonorResource"
 * ),
 * )
 */
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
            'donorInfo' => new ProfileDonorResource($this->whenLoaded('donor')),
        ];
    }
}
