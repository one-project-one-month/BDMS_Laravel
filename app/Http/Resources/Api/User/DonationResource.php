<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'donationDate' => $this->donation_date,
            'status' => $this->status,
            'approvedAt' => $this->approved_at,
            'remarks' => $this->remarks,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
