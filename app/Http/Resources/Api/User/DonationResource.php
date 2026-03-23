<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="UserDonationResource",
 * title="User Donation Resource (User Only)",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="donorId", type="integer", example=3),
 * @OA\Property(property="hospitalName", type="string", example="Yangon Hospital"),
 * @OA\Property(property="bloodGroup", type="string", example="A+"),
 * @OA\Property(property="donationDate", type="date", example="01-01-20xx"),
 * @OA\Property(property="status", type="string", example="pending"),
 * @OA\Property(property="approvedAt", type="date", example="01-01-20xx"),
 * @OA\Property(property="remarks", type="string", example="Hello from BDMS Laravel!"),
 * @OA\Property(property="createdAt", type="datetime", example="01-01-20xx 12:00:00"),
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
            'id' => $this->id,
            'donorId' => $this->donor_id,
            'hospitalName' => $this->hospital->name,
            'bloodGroup' => $this->blood_group,
            'unitsDonated' => $this->units_donated,
            'donationDate' => $this->donation_date->format('d-m-Y'),
            'status' => $this->status,
            'approvedAt' => $this->approved_at->format('d-m-Y'),
            'remarks' => $this->remarks,
            'createdAt' => $this->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
