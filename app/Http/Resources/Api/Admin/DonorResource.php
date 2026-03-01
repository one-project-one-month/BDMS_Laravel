<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="DonorResource",
 * title="Donor Resource",
 * description="Donor model schema",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="userId", type="integer", example="5"),
 * @OA\Property(property="blood_group", type="string", example="O+"),
 * @OA\Property(property="gender", type="string", example="male"),
 * @OA\Property(property="age", type="integer", example=26),
 * @OA\Property(property="lastDonation", type="string", format="date", example="2025-12-01"),
 * @OA\Property(property="isActive", type="boolean", example=true)
 * )
 */
class DonorResource extends JsonResource
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
            "userId" => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'bloodGroup' => $this->blood_group,
            'gender' => $this->gender,
            'age' => \Carbon\Carbon::parse($this->date_of_birth)->age,
            'lastDonation' => $this->last_donation_date?->format('Y-m-d'),
            'isActive' => $this->is_active,
        ];
    }
}
