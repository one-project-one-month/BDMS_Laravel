<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Resources\Api\Admin\HospitalResource;
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
            'remarks' => $this->remarks,
            'bloodGroup' => $this->blood_group,
            'unitsDonated' => $this->units_donated,
            'status' => $this->status,

            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'bloodRequest' => $this->whenLoaded('bloodRequest'),
            'donor' => $this->whenLoaded('donor'),
            'createdBy' => $this->whenLoaded('creator'),
            'approvedBy' => $this->whenLoaded('approver'),

            'donationDate' => $this->donation_date?->format('Y-m-d'),
            'approvedAt' => $this->approved_at?->format('Y-m-d'),
            'createdAt' => $this->created_at?->format('Y-m-d'),
            'updatedAt' => $this->updated_at?->format('Y-m-d'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d'),
        ];
    }
}
