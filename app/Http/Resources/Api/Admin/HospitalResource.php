<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="HospitalResource",
 * title="Hospital Resource",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Yangon General Hospital"),
 * @OA\Property(property="address", type="string", example="Rosemary Street"),
 * @OA\Property(property="phone", type="string", example="09xxxxxxxx"),
 * @OA\Property(property="email", type="string", example="ygn@hospital.com"),
 * @OA\Property(property="isActive", type="boolean", example="true"),
 * @OA\Property(property="isVerified", type="boolean", example="true"),
 * @OA\Property(property="createdAt", type="datetime", example="2026-01-01")
 * )
 */
class HospitalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "address" => $this->address,
            "phone" => $this->phone,
            "email" => $this->email,
            "isActive" => $this->is_active,
            "isVerified" => $this->is_verified,
            "createdAt" => $this->created_at->format("Y-m-d H:i:s"),
        ];
    }
}
