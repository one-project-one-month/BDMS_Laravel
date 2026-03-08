<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodInventoryResource extends JsonResource
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
            'donationId' => $this->donation_id,
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'bloodGroup' => $this->blood_group,
            'units' => $this->units,
            'collectedAt' => $this->collected_at?->format('Y-m-d'),
            'expiredAt' => $this->expired_at?->format('Y-m-d'),
            'status' => $this->status,
            'bloodRequest' => new BloodRequestResource($this->whenLoaded('bloodRequest')),
            'createdAt' => $this->created_at?->format('Y-m-d'),
            'updatedAt' => $this->updated_at?->format('Y-m-d'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d'),
        ];
    }
}
