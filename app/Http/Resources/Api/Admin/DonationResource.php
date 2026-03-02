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
 * @OA\Property(property="donationId", type="integer", example="5"),
 * @OA\Property(property="donorId", type="integer", example="5"),
 * @OA\Property(property="hospitalId", type="integer", example="5"),
 * @OA\Property(property="bloodRequestId", type="integer", example="5"),
 * @OA\Property(property="createdBy", type="object", example={"id": 1, "name": "John Doe"}),
 * @OA\Property(property="bloodGroup", type="string", example="O+"),
 * @OA\Property(property="unitsDonated", type="integer", example=5),
 * @OA\Property(property="donationDate", type="string", format="date", example="2025-12-01"),
 * @OA\Property(property="status", type="string", example="pending"),
 * @OA\Property(property="approvedBy", type="object", example={"id": 1, "name": "John Doe"}),
 * @OA\Property(property="approvedAt", type="string", format="date", example="2025-12-01"),
 * @OA\Property(property="remarks", type="string", example="Donation completed successfully"),
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
            "donationId" => $this->donationId,
            "donorId" => $this->donorId,
            "hospitalId" => $this->hospitalId,
            "bloodRequestId" => $this->bloodRequestId,
            "createdBy" => new UserResource($this->whenLoaded('creator')),
            "bloodGroup" => $this->bloodGroup,
            "unitsDonated" => $this->unitsDotoArraynated,
            "donationDate" => $this->donationDate,
            "status" => $this->status,
            "approvedBy" => new UserResource($this->whenLoaded('approver')),
            "approvedAt" => $this->approvedAt,
            "remarks" => $this->remarks,
        ];
    }
}
