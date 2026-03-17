<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="DonationResource",
 * title="Donation Resource",
 * description="Donation model schema",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="donor", type="object", ref="#/components/schemas/DonorResource"),
 * @OA\Property(property="hospital", type="object", ref="#/components/schemas/HospitalResource"),
 * @OA\Property(property="bloodRequestId", type="integer", example=3, nullable=true),
 * @OA\Property(property="createdBy", type="object", ref="#/components/schemas/UserResource"),
 * @OA\Property(property="bloodGroup", type="string", example="O+"),
 * @OA\Property(property="unitsDonated", type="integer", example=1),
 * @OA\Property(property="donationDate", type="string", format="date", example="2026-03-01"),
 * @OA\Property(property="status", type="string", example="pending"),
 * @OA\Property(property="approvedBy", type="object", ref="#/components/schemas/UserResource", nullable=true),
 * @OA\Property(property="approvedAt", type="string", format="date-time", nullable=true),
 * @OA\Property(property="remarks", type="string", example="Standard blood donation.", nullable=true),
 * @OA\Property(property="createdAt", type="string", format="date-time", example="2026-03-01 10:00:00"),
 * )
 */
class DonationResource extends JsonResource
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
            'donor' => new DonorResource($this->whenLoaded('donor')),
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            "bloodRequestId" => $this->blood_request_id,
            "createdBy" => new UserResource($this->whenLoaded('creator')),
            "bloodGroup" => $this->blood_group,
            "unitsDonated" => $this->units_donated,
            "donationDate" => $this->donation_date?->format('Y-m-d'),
            "status" => $this->status,
            "approvedBy" => new UserResource($this->whenLoaded('approver')),
            "approvedAt" => $this->approved_at?->format('Y-m-d'),
            "remarks" => $this->remarks,
            "createdAt" => $this->created_at?->format('Y-m-d'),
        ];
    }
}
